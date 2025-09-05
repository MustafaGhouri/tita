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
        return view('dashboard', [
            'activeEmployees' => Employee::where('status','ACTIVE')->count(),
            'diagCompleted'   => DiagnosticResult::count(),
            'evalsThisMonth'  => MysteryEvaluation::where('monthKey', now()->format('Y-m'))->count(),
            'upcomingCoach'   => CoachingSession::whereBetween('follow_up_date',[now(), now()->addDays(30)])->count(),
            'recent'          => MysteryEvaluation::latest()->take(5)->get(),
        ]);
    }
}
