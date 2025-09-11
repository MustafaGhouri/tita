<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\MysteryEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MysteryShopperController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:client-only');
    }

    // Employees list (lightweight)
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
			$employees = Employee::query()
			->when($q, fn($x) => $x->where('first_name','like',"%{$q}%")
			->orWhere('employee_code','like',"%{$q}%"))
			->withCount('evaluations')   // 👈 so blade can read $e->evaluations_count
			->orderBy('first_name')
			->paginate(12);
           

        return view('mystery.index', compact('employees', 'q'));
    }

    // Employee profile page (screenshot UI)
    public function employee(Employee $employee)
    {
        // latest evaluations (newest first)
        $archive = $employee->evaluations()->latest()->get();

        // optionally eager-load creator relation if model has it
        if (method_exists(\App\Models\MysteryEvaluation::class, 'creator')) {
            $archive->load('creator');
        }

        return view('mystery.employee', [
            'employee' => $employee,
            'archive'  => $archive,
             'canNewMystery' => !$employee->evaluations()->where('monthKey', now()->format('Y-m'))->exists(),
        ]);
    }
}
