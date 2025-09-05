<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DiagnosticSubmitRequest;
use App\Models\Employee;
use App\Models\DiagnosticResult;
use App\Models\CoachingSession;
use App\Models\MysteryEvaluation;
use App\Models\TestTemplate;
// app/Http/Controllers/DiagnosticController.php
class DiagnosticController extends Controller {
    // CLIENT can launch for any employee; EMPLOYEE can launch for self
    public function index(Employee $employee){
        $template = TestTemplate::where('is_active',true)->firstOrFail();
        $existing = $employee->diagnosticResult;
        return view('diagnostic.index', compact('employee','template','existing'));
    }

    public function start(Employee $employee){
        $this->authorizeRun($employee);
        if ($employee->diagnosticResult) {
            return back()->withErrors('Diagnostic already completed.');
        }
        $template = TestTemplate::where('is_active',true)->firstOrFail();
        return view('diagnostic.form', compact('employee','template'));
    }

    public function submit(DiagnosticSubmitRequest $req, Employee $employee){
        $this->authorizeRun($employee);
        if ($employee->diagnosticResult) { return back()->withErrors('Already completed.'); }

        $tpl = TestTemplate::where('is_active',true)->firstOrFail();
        $answers = $req->input('answers');

        $score = $this->autoScore($tpl->schema, $answers);
        if ($req->filled('manual_score')) {
            $score = min(100, max(0, $score + floatval($req->manual_score)));
        }

        $res = DiagnosticResult::create([
            'employee_id'=>$employee->id,
            'answers'    =>$answers,
            'score'      =>$score,
            'submitted_at'=>now(),
            'manual_score'=>$req->input('manual_score')
        ]);

        return redirect()->route('diagnostic.view',$employee)->with('ok','Diagnostic completed.');
    }

    public function view(Employee $employee){
        $result = $employee->diagnosticResult;
        abort_unless($result, 404);
        return view('diagnostic.view', compact('employee','result'));
    }

    public function pdf(Employee $employee){
        $result = $employee->diagnosticResult;
        abort_unless($result, 404);
        $pdf = \PDF::loadView('reports.diagnostic', compact('employee','result'));
        return $pdf->download("diagnostic-{$employee->id}.pdf");
    }

    private function authorizeRun(Employee $employee){
        $u = auth()->user();
        if ($u->isClient()) return;
        if ($u->isEmployee() && $u->email === $employee->email) return;
        abort(403);
    }

    private function autoScore(array $schema, array $answers): float {
        $totalW = 0; $sum = 0;
        foreach($schema['items'] as $i){
            $w = floatval($i['weight'] ?? 1);
            $totalW += $w;
            $key = $i['key']; $val = $answers[$key] ?? null;
            $p = 0;
            switch($i['type']){
                case 'mcq':
                    $p = (isset($i['correctIndex']) && intval($val) === intval($i['correctIndex'])) ? 1 : 0;
                    break;
                case 'boolean':
                    $p = ($val==true && ($i['correct'] ?? true)==true) ? 1 : (($val==false && ($i['correct'] ?? false)==false) ? 1 : 0);
                    break;
                case 'scale':
                    $min = $i['min'] ?? 1; $max = $i['max'] ?? 5;
                    if ($max>$min && $val!==null) $p = (float)($val-$min)/($max-$min);
                    break;
                case 'short_text':
                    $p = 0; // subjective; can be added via manual_score
                    break;
            }
            $sum += $p * $w;
        }
        return $totalW>0 ? round(($sum/$totalW)*100,2) : 0.0;
    }
}
