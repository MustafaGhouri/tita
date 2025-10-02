<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\MysteryEvaluation;
use App\Models\MysteryChecklist;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class MysteryEvaluationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:client-only');
    }

    /** Build a unique session key (company + employee) for one-time unlock */
    private function unlockKey(int $employeeId): string
    {
        $companyId = auth()->user()->company_id ?? 0;
        return "mystery.eval.unlock.{$companyId}.{$employeeId}";
    }

    /**
     * POST /employees/{employee}/mystery/unlock
     * Verify the company code saved in companies.company_code and unlock once.
     */
    public function unlock(Request $request, Employee $employee)
    {
        $request->validate(['code' => ['required', 'string', 'max:20']]);

        $company = auth()->user()->company ?? null; // User::belongsTo(Company::class,'company_id')
        $dbCode  = (string) ($company->company_code ?? '');
        $input   = (string) trim($request->input('code', ''));

        if ($dbCode === '' || !hash_equals($dbCode, $input)) {
            return back()->withErrors(['code' => 'Código de seguridad inválido.'])->withInput();
        }

        // One-time unlock for this employee
        session([$this->unlockKey($employee->id) => true]);

        return redirect()->route('mystery.create', $employee);
    }

    /**
     * GET /employees/{employee}/mystery/create
     * Only open if unlocked via unlock().
     */
    public function create(Request $request, Employee $employee)
    {
        try {
            // 🔐 gate: require session unlock
            if (!session($this->unlockKey($employee->id), false)) {
                return redirect()
                    ->route('mystery.employee', $employee)
                    ->withErrors('Please enter your company security code to start an evaluation.');
            }

            $month = now()->format('Y-m');

            // 1 eval per month per employee
            if ($employee->evaluations()->where('monthKey', $month)->exists()) {
                return back()->withErrors('Only one evaluation per month is allowed.');
            }

            // Load active checklist
            $checklist = MysteryChecklist::where('is_active', true)->first();
            if (!$checklist) {
                return back()->withErrors('No active checklist found. Please activate a checklist first.');
            }

            // Ensure schema is array for the Blade view
            $schema = $checklist->schema;
            if (is_string($schema)) {
                $decoded = json_decode($schema, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $checklist->schema = $decoded;
                } else {
                    return back()->withErrors('Checklist schema JSON is invalid.');
                }
            }

            return view('mystery.create', compact('employee', 'checklist', 'month'));
        } catch (Exception $e) {
            return back()->withErrors('Failed to open form: ' . $e->getMessage());
        }
    }

    /**
     * POST /employees/{employee}/mystery
     * Save evaluation — requires and CONSUMES unlock.
     */
    public function store(Request $req, Employee $employee)
    {
        // 🔐 must be unlocked just before this (single-use)
        $key = $this->unlockKey($employee->id);
        if (!session()->pull($key, false)) {
            return redirect()
                ->route('mystery.employee', $employee)
                ->withErrors(['code' => 'Session is not unlocked. Please enter the company code again.']);
        }

        // Basic validation
        $req->validate([
            'monthKey'     => ['required', 'string', 'max:20'],
            'checklist_id' => ['required', 'integer', 'exists:mystery_checklists,id'],
            'answers'      => ['required'],
            'video'        => ['required','file','mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska','max:307200'], // ~300MB
        ]);

        // 1 per month
        if ($employee->evaluations()->where('monthKey', $req->monthKey)->exists()) {
            return back()->withErrors(['monthKey' => 'Only one evaluation per month is allowed.'])->withInput();
        }

        // Normalize answers to array
        $answers = $req->input('answers');
        if (is_string($answers)) {
            $answers = json_decode($answers, true);
        }
        if (!is_array($answers)) {
            return back()->withErrors(['answers' => 'Invalid answers payload.'])->withInput();
        }

        // Load checklist + normalize schema
        $checklist = MysteryChecklist::findOrFail($req->checklist_id);
        $schema = $checklist->schema;
        if (is_string($schema)) {
            $schema = json_decode($schema, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['checklist_id' => 'Checklist schema JSON is invalid.']);
            }
        }

        // Compute score (0..100)
        $score = $this->computeWeightedScore($schema, $answers);

        // --- Video upload into public/videos/... so it’s web-visible
        $file = $req->file('video');
        if (!$file || !$file->isValid()) {
            Log::error('Video invalid', [
                'err' => $file ? $file->getError() : 'no-file',
                'ini' => [
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size'       => ini_get('post_max_size'),
                    'upload_tmp_dir'      => ini_get('upload_tmp_dir'),
                    'sys_tmp'             => sys_get_temp_dir(),
                ],
            ]);
            return back()->withErrors(['video' => 'Upload failed before saving.'])->withInput();
        }

        $companyId = auth()->user()->company_id ?? 'company';
        $dirRel    = "videos/{$companyId}/{$employee->id}";
        $dirAbs    = public_path($dirRel);

        if (!is_dir($dirAbs)) {
            @mkdir($dirAbs, 0775, true);
        }

        $name = 'eval-' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        try {
            $moved = $file->move($dirAbs, $name); // move_uploaded_file()
            if (!$moved) {
                throw new \RuntimeException('move() returned false');
            }
        } catch (\Throwable $e) {
            Log::error('Public move failed', [
                'target' => $dirAbs . DIRECTORY_SEPARATOR . $name,
                'err'    => $e->getMessage(),
                'last'   => error_get_last(),
            ]);
            return back()->withErrors(['video' => 'Video upload failed on server.'])->withInput();
        }

        $relativePathForDb = $dirRel . '/' . $name;

        // Create evaluation
        $eval = MysteryEvaluation::create([
            'company_id'  => auth()->user()->company_id ?? null,
            'employee_id' => $employee->id,
            'checklist_id'=> $checklist->id,
            'monthKey'    => $req->monthKey,
            'answers'     => $answers,  // model should cast to json
            'score'       => $score,
            'video_path'  => $relativePathForDb,
            'created_by'  => auth()->id(),
        ]);

        return redirect()->route('mystery.show', $eval)->with('ok', 'Evaluation saved.');
    }

    public function show(MysteryEvaluation $evaluation)
    {
        return view('mystery.show', compact('evaluation'));
    }

    public function pdf(MysteryEvaluation $evaluation)
    {
        $pdf = \PDF::loadView('reports.mystery', ['evaluation' => $evaluation]);
        $file = 'mystery-' . $evaluation->id . '.pdf';
        return $pdf->download($file);
    }

    /**
     * Compute a 0..100 score from a sections-based schema and given answers.
     * - yes_no: yes=1, no=0
     * - yes_no_na: yes=1, no=0, na => item excluded from total weight (no penalty)
     * - scale/range: proportional (val-min)/(max-min)
     * - other types: contribute 0 unless non-zero weight provided
     */
    private function computeWeightedScore(array $schema, array $answers): float
    {
        // Collect items from schema.items[] or schema.sections[].items[]
        $items = [];
        if (isset($schema['sections']) && is_array($schema['sections'])) {
            foreach ($schema['sections'] as $sec) {
                foreach (($sec['items'] ?? []) as $it) {
                    $items[] = $it;
                }
            }
        } elseif (isset($schema['items']) && is_array($schema['items'])) {
            $items = $schema['items'];
        }

        $totalWeight = 0.0;
        $sum = 0.0;

        foreach ($items as $item) {
            $key = $item['key'] ?? null;
            if (!$key) continue;

            $type = $item['type'] ?? 'text';
            $w    = (float)($item['weight'] ?? 0);
            if ($w <= 0) continue; // zero-weight items do not affect score

            $val = $answers[$key] ?? null;
            $earned = 0.0;
            $countsInDenominator = true;

            switch ($type) {
                case 'yes_no':
                    $isYes = ($val === 'yes' || $val === 1 || $val === true || $val === '1');
                    $earned = $isYes ? $w : 0.0;
                    break;

                case 'yes_no_na':
                    if (in_array(strtolower((string)$val), ['na','n/a'])) {
                        $countsInDenominator = false; // exclude
                        $earned = 0.0;
                    } else {
                        $isYes = ($val === 'yes' || $val === 1 || $val === true || $val === '1');
                        $earned = $isYes ? $w : 0.0;
                    }
                    break;

                case 'scale':
                case 'range':
                    $min = isset($item['min']) ? (float)$item['min'] : 1.0;
                    $max = isset($item['max']) ? (float)$item['max'] : 5.0;
                    if ($val !== null && is_numeric($val) && $max > $min) {
                        $ratio = ((float)$val - $min) / ($max - $min);
                        $ratio = max(0.0, min(1.0, $ratio));
                        $earned = $w * $ratio;
                    }
                    break;

                default:
                    // text/number/select/note etc.: keep as 0
                    break;
            }

            if ($countsInDenominator) $totalWeight += $w;
            $sum += $earned;
        }

        if ($totalWeight <= 0) return 0.0;
        return round(($sum / $totalWeight) * 100, 2);
    }
}
