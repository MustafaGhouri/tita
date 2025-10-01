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

    // public function store(CoachingSessionRequest $req, Employee $employee){
    //     $data = $req->validated();
    //     $data['employee_id'] = $employee->id;
    //     $data['company_id'] = auth()->user()->company_id;
    //     $data['created_by'] = auth()->id();

    //     // attachments
    //     if ($req->hasFile('attachments')) {
    //         $paths = [];
    //         foreach($req->file('attachments') as $f){
    //             $paths[] = $f->store("coach/".auth()->user()->company_id.'/'.$employee->id, 'public');
    //         }
    //         $data['attachments'] = $paths;
    //     }
    //     CoachingSession::create($data);
    //     return redirect()->route('coaching.index',$employee)->with('ok','Saved.');
    // }

     public function store(CoachingSessionRequest $req, Employee $employee)
    {
        $data = $req->validated();
        $data['employee_id'] = $employee->id;
        $data['company_id'] = auth()->user()->company_id;
        $data['created_by'] = auth()->id();
          return $req->all();
        // attachments -> Cloudinary (URLs save honge)
        if ($req->hasFile('attachments')) {
            $urls = [];

            foreach ((array) $req->file('attachments') as $file) {
                $isVideo = Str::startsWith($file->getMimeType(), 'video');

                $uploaded = Cloudinary::uploadFile(
                    $file->getRealPath(),
                    [
                        'folder'        => 'coach/' . auth()->user()->company_id . '/' . $employee->id,
                        'resource_type' => $isVideo ? 'video' : 'auto', // videos ke liye 'video' zaroori
                        // 'public_id'   => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), // optional
                    ]
                );

                // sirf URL save kar rahe hain (simple drop-in replacement)
                $urls[] = $uploaded->getSecurePath();

                // — Agar aap future me delete/transform karna chahen to iski jagah yeh store karein:
                // $urls[] = [
                //     'url'       => $uploaded->getSecurePath(),
                //     'public_id' => $uploaded->getPublicId(),
                //     'type'      => $isVideo ? 'video' : 'file',
                // ];
            }

            $data['attachments'] = $urls; // JSON column par cast ho to direct array save ho jayega
        }
      
        //CoachingSession::create($data);

        return $data;
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
