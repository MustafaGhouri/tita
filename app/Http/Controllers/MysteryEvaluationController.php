<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\DiagnosticResult;
use App\Models\CoachingSession;
use App\Models\MysteryEvaluation;
use App\Models\MysteryChecklist;
use Exception;
use App\Http\Requests\MysteryEvaluationStoreRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // ✅ yehi correct hai
use Illuminate\Support\Facades\DB;       // ✅ Correct import

// app/Http/Controllers/MysteryEvaluationController.php
class MysteryEvaluationController extends Controller {
    public function __construct(){ $this->middleware('can:client-only'); }

    public function create(Employee $employee) {
    	try{ 

        $month = now()->format('Y-m');
        
        if ($employee->evaluations()->where('monthKey',$month)->exists()){
            return back()->withErrors('Only one evaluation per month is allowed.');
        }
        // dd($employee); // yaha tk dd print hojata hai
        $checklist = MysteryChecklist::where('is_active',true)->firstOrFail();
         // dd($checklist); // ye wala dd lgane pe 404 error ata hai route pe
        return view('mystery.create', compact('employee','checklist','month'));
    } catch (Exception $e) {
    	dd($e->getMessage());
    }
     
    }
  
    // //../req  MysteryEvaluationStoreRequest 
    // public function store(Request $req, Employee $employee) {
    //     // 1/month enforce (db unique bhi hai)
    //     if ($employee->evaluations()->where('monthKey',$req->monthKey)->exists()){
    //         return back()->withErrors('Only one evaluation per month is allowed.');
    //     }

    //     $answers = $req->input('answers');
    //     $checklist = MysteryChecklist::findOrFail($req->checklist_id);
    //     $score = $this->computeWeightedScore($checklist->schema, $answers);

    //     // store video
    //     $file = $req->file('video');
    //     $company = auth()->user()->company_id;
    //     $dir = "videos/{$company}/{$employee->id}";
    //     $name = 'eval-'.Str::uuid().'.'.$file->getClientOriginalExtension();
    //     $path = $file->storeAs($dir, $name, 'public');

    //     $eval = MysteryEvaluation::create([
    //         'employee_id' => $employee->id,
    //         'checklist_id'=> $checklist->id,
    //         'monthKey'    => $req->monthKey,
    //         'answers'     => $answers,
    //         'score'       => $score,
    //         'video_path'  => $path,
    //         'created_by'  => auth()->id(),
    //     ]);

    //     return redirect()->route('mystery.show', $eval)->with('ok','Evaluation saved.');
    // }
    
    
    
    public function store(Request $req, Employee $employee)
{
    // Basic validation
    $req->validate([
        'monthKey'     => ['required','string','max:20'],
        'checklist_id' => ['required','integer','exists:mystery_checklists,id'],
        'answers'      => ['required'],
        'video'        => ['required','file','mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska','max:307200'], // 300MB
    ]);

    // 1 per month
    if ($employee->evaluations()->where('monthKey',$req->monthKey)->exists()){
        return back()->withErrors(['monthKey' => 'Only one evaluation per month is allowed.'])->withInput();
    }

    // Answers normalize
    $answers = is_string($req->answers) ? json_decode($req->answers, true) : $req->answers;
    if (!is_array($answers)) {
        return back()->withErrors(['answers' => 'Invalid answers payload.'])->withInput();
    }

    $checklist = MysteryChecklist::findOrFail($req->checklist_id);
    $schema    = is_string($checklist->schema) ? json_decode($checklist->schema, true) : $checklist->schema;
    $score     = $this->computeWeightedScore($schema, $answers);

    // --- Video upload to public_html directly ---
    $file = $req->file('video');
    if (!$file || !$file->isValid()) {
        Log::error('Video invalid', [
            'err' => $file ? $file->getError() : 'no-file',
            'tmp' => $file ? $file->getPathname() : null,
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
    $dirRel    = "videos/{$companyId}/{$employee->id}";    // relative under public_html
    $dirAbs    = public_path($dirRel);                      // absolute path to public_html/videos/...
    if (!is_dir($dirAbs)) {
        @mkdir($dirAbs, 0775, true);
    }

    $name = 'eval-'.Str::uuid().'.'.$file->getClientOriginalExtension();
    $full = rtrim($dirAbs, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$name;

    try {
        // Most cPanel-friendly: move() uses move_uploaded_file()
        $moved = $file->move($dirAbs, $name);
        if (!$moved) {
            throw new \RuntimeException('move() returned false');
        }
    } catch (\Throwable $e) {
        Log::error('Public move failed', [
            'target' => $full,
            'err'    => $e->getMessage(),
            'last'   => error_get_last(),
        ]);
        return back()->withErrors(['video' => 'Video upload failed on server.'])->withInput();
    }

    $relativePathForDb = $dirRel.'/'.$name; // e.g. videos/1/10/eval-uuid.mp4 (web visible)

    $eval = MysteryEvaluation::create([
        'employee_id' => $employee->id,
        'checklist_id'=> $checklist->id,
        'monthKey'    => $req->monthKey,
        'answers'     => $answers,
        'score'       => $score,
        'video_path'  => $relativePathForDb,
        'created_by'  => auth()->id(),
    ]);

    return redirect()->route('mystery.show', $eval)->with('ok','Evaluation saved.');
}

    
    public function show(MysteryEvaluation $evaluation) {
        return view('mystery.show', compact('evaluation'));
    }

    public function pdf(MysteryEvaluation $evaluation){
        $pdf = \PDF::loadView('reports.mystery', ['evaluation'=>$evaluation]);
        $file = 'mystery-'.$evaluation->id.'.pdf';
        return $pdf->download($file);
    }

    private function computeWeightedScore(array $schema, array $answers): float {
        $totalW = 0; $sum = 0;
        foreach ($schema['items'] as $item) {
            $key = $item['key']; $w = floatval($item['weight'] ?? 1);
            $totalW += $w;

            $val = $answers[$key] ?? null;
            $p = 0;
            switch($item['type']){
                case 'yes_no':   $p = ($val==='yes' || $val===1 || $val===true) ? 1 : 0; break;
                case 'scale':    // assume min..max
                    $min = $item['min'] ?? 1; $max = $item['max'] ?? 5;
                    if ($max>$min && $val!==null) $p = (float)($val-$min)/($max-$min);
                    break;
                case 'note':     $p = 0; break; // note doesn't count, weight can be 0
            }
            $sum += $p * $w;
        }
        if ($totalW <= 0) return 0.0;
        return round(($sum / $totalW) * 100, 2); // percentage 0..100
    }
}
