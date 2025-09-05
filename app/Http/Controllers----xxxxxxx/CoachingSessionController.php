<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\CoachingSessionRequest;
use App\Models\Employee;

use App\Models\CoachingSession;

// app/Http/Controllers/CoachingSessionController.php
class CoachingSessionController extends Controller {
    public function __construct(){ $this->middleware('can:client-only'); }

    public function index(Employee $employee){
        $list = CoachingSession::when($employee->id, fn($q)=>$q->where('employee_id',$employee->id))
                ->orderByDesc('date')->paginate(10);
        $due = request('due')==='soon'
            ? CoachingSession::whereBetween('follow_up_date',[now(), now()->addDays(30)])->get()
            : collect();
        return view('coaching.index', compact('employee','list','due'));
    }

    public function create(Employee $employee){
        return view('coaching.form', ['employee'=>$employee, 'session'=>new CoachingSession]);
    }

    public function store(CoachingSessionRequest $req, Employee $employee){
        $data = $req->validated();
        $data['employee_id'] = $employee->id;
        $data['company_id'] = auth()->user()->company_id;
        $data['created_by'] = auth()->id();

        // attachments
        if ($req->hasFile('attachments')) {
            $paths = [];
            foreach($req->file('attachments') as $f){
                $paths[] = $f->store("coach/".auth()->user()->company_id.'/'.$employee->id, 'public');
            }
            $data['attachments'] = $paths;
        }
        CoachingSession::create($data);
        return redirect()->route('coaching.index',$employee)->with('ok','Saved.');
    }

    public function edit(Employee $employee, CoachingSession $session){
        return view('coaching.form', compact('employee','session'));
    }

    public function update(CoachingSessionRequest $req, Employee $employee, CoachingSession $session){
        $data = $req->validated();
        if ($req->hasFile('attachments')) {
            $paths = $session->attachments ?? [];
            foreach($req->file('attachments') as $f){
                $paths[] = $f->store("coach/".auth()->user()->company_id.'/'.$employee->id, 'public');
            }
            $data['attachments'] = $paths;
        }
        $session->update($data);
        return redirect()->route('coaching.index',$employee)->with('ok','Updated.');
    }
}
