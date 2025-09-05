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
                ->orWhere('position','like',"%$s%")
                ->orWhere('status','like',"%$s%"));
        }
        if ($r->get('only_active')) {
            $q->where('status','ACTIVE');
        }
        $employees = $q->orderBy('first_name')->paginate(10)->withQueryString();
        return view('employees.index', compact('employees','s'));
    }

    public function create(){ return view('employees.form', ['employee'=>new Employee]); }

    // public function store(EmployeeStoreRequest $req){
    //     Employee::create($req->validated());
    //     return redirect()->route('employees.index')->with('ok','Employee added.');
    // }
    
    public function store(EmployeeStoreRequest $req)
    {
        $data = $req->validated();

        // Always assign company_id from the authenticated user
        $data['company_id'] = auth()->user()->company_id;

        // Normalize optional fields (trim)
        $data['first_name'] = trim($data['first_name']);
        $data['last_name']  = isset($data['last_name']) ? trim($data['last_name']) : null;
        $data['email']      = isset($data['email']) ? strtolower(trim($data['email'])) : null;
        $data['position']   = isset($data['position']) ? trim($data['position']) : null;

        // Status to canonical values
        if (isset($data['status'])) {
            $data['status'] = strtoupper($data['status']); // e.g., ACTIVE / INACTIVE
        } else {
            $data['status'] = 'ACTIVE';
        }

        Employee::create($data);

        return redirect()
            ->route('employees.index')
            ->with('ok', 'Employee added.');
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

    // public function update(EmployeeUpdateRequest $req, Employee $employee){  //EmployeeUpdateRequest $req, 
    //     $employee->update($req->validated());
    //     return redirect()->route('employees.show',$employee)->with('ok','Updated.');
    // }
    
    
    public function update(\App\Http\Requests\EmployeeUpdateRequest $req, \App\Models\Employee $employee)
{
    // (Multi-tenant safety) prevent updating employees from another company
    if (auth()->user()->company_id !== $employee->company_id) {
        abort(403, 'Unauthorized.');
    }

    // Validate via FormRequest
    $data = $req->validated();

    // Never allow company_id to be changed from the request
    $data['company_id'] = $employee->company_id;

    // Normalize fields
    $data['first_name'] = trim($data['first_name']);
    $data['last_name']  = array_key_exists('last_name', $data)  && $data['last_name']  !== null ? trim($data['last_name'])  : null;
    $data['email']      = array_key_exists('email', $data)      && $data['email']      !== null ? strtolower(trim($data['email'])) : null;
    $data['position']   = array_key_exists('position', $data)   && $data['position']   !== null ? trim($data['position']) : null;

    if (array_key_exists('status', $data) && $data['status'] !== null) {
        $data['status'] = strtoupper($data['status']); // ACTIVE / INACTIVE
        if (!in_array($data['status'], ['ACTIVE','INACTIVE'], true)) {
            unset($data['status']); // safety: ignore invalid
        }
    }

    // Update
    $employee->update($data);

    return redirect()
        ->route('employees.show', $employee)
        ->with('ok', 'Updated.');
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
