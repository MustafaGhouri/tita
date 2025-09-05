<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;                       // 👈 model import
use App\Http\Requests\EmployeeStoreRequest;   // 👈 request import
use App\Http\Requests\EmployeeUpdateRequest;  // 👈 request import

// app/Http/Controllers/EmployeeController.php
class EmployeeController extends Controller {
    public function __construct(){ $this->middleware('can:client-only'); }

    public function index(Request $r) {
        $q = Employee::query();
        if ($s = $r->get('s')) {
            $q->where(fn($q)=>$q->where('first_name','like',"%$s%")
                ->orWhere('last_name','like',"%$s%")
                ->orWhere('email','like',"%$s%")
                ->orWhere('position','like',"%$s%"));
        }
        $employees = $q->orderBy('first_name')->paginate(10)->withQueryString();
        return view('employees.index', compact('employees','s'));
    }

    public function create(){ return view('employees.form', ['employee'=>new Employee]); }

    public function store(EmployeeStoreRequest $req){
        Employee::create($req->validated());
        return redirect()->route('employees.index')->with('ok','Employee added.');
    }

    public function show(Employee $employee){
        return view('employees.show', [
            'employee'=>$employee,
            'latestEval'=> $employee->evaluations()->latest()->first(),
            'diagnostic'=> $employee->diagnosticResult,
            'canNewMystery' => !$employee->evaluations()->where('monthKey', now()->format('Y-m'))->exists(),
        ]);
    }

    public function edit(Employee $employee){ return view('employees.form', compact('employee')); }

    public function update(EmployeeUpdateRequest $req, Employee $employee){
        $employee->update($req->validated());
        return redirect()->route('employees.show',$employee)->with('ok','Updated.');
    }

    public function destroy(Employee $employee){
        $employee->delete();
        return back()->with('ok','Deleted.');
    }

    public function toggle(Employee $employee){
        $employee->update(['status'=>$employee->status==='ACTIVE'?'INACTIVE':'ACTIVE']);
        return back()->with('ok','Status changed.');
    }
}
