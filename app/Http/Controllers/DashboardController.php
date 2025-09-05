<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\DiagnosticResult;
use App\Models\CoachingSession;
use App\Models\MysteryEvaluation;
// app/Http/Controllers/DashboardController.php
class DashboardController extends Controller {
    public function __invoke() {
        $cid = auth()->user()->company_id;

        // Latest 5 evaluations & 5 diagnostics (with employee)
        $recentEval = MysteryEvaluation::with('employee')
            ->latest() // uses created_at
            ->take(5)->get();

        $recentDiag = DiagnosticResult::with('employee')
            ->latest('submitted_at') // use submitted_at for diagnostics
            ->take(5)->get();

        // Merge + sort newest-first
        $recentActivities = collect()
            ->merge(
                $recentEval->map(function ($e) {
                    return [
                        'type'     => 'evaluation',
                        'employee' => optional($e->employee)->name ?? 'Employee',
                        'when'     => $e->created_at,
                        'url'      => route('mystery.show', $e),
                        'label'    => 'completed mystery evaluation',
                    ];
                })
            )
            ->merge(
                $recentDiag->map(function ($d) {
                    $url = \Illuminate\Support\Facades\Route::has('diagnostics.show')
                        ? route('diagnostics.show', $d)
                        : '#'; // agar route na ho to # rakho
                    return [
                        'type'     => 'diagnostic',
                        'employee' => optional($d->employee)->name ?? 'Employee',
                        'when'     => $d->submitted_at,
                        'url'      => $url,
                        'label'    => 'completed diagnostic test',
                        // optional: 'score' => $d->final_score,
                    ];
                })
            )
            ->sortByDesc('when')
            ->take(8)
            ->values();

        return view('dashboard', [
            'activeEmployees' => Employee::where('status','ACTIVE')->count(),
            'diagCompleted'   => DiagnosticResult::count(),
            'evalsThisMonth'  => MysteryEvaluation::where('monthKey', now()->format('Y-m'))->count(),
            'upcomingCoach'   => CoachingSession::whereBetween('follow_up_date',[now(), now()->addDays(30)])->count(),
            // old: 'recent' => MysteryEvaluation::latest()->take(5)->get(),
            'recentActivities'=> $recentActivities, // <-- yeh naya mixed feed
        ]);
    }
}
