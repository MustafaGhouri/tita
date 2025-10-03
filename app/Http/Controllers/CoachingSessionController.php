<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\CoachingSessionRequest;
use App\Models\Employee;

// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; // ensure this import

use Cloudinary\Api\Upload\UploadApi;

use App\Models\CoachingSession; // <-- confirm your model namespace
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

    /**
     * Store a new coaching session.
     *
     * @param CoachingSessionRequest $req
     * @param Employee $employee
     *
     * @return \Illuminate\Http\RedirectResponse
     */
     public function store(CoachingSessionRequest $req, Employee $employee)
    {
        $data = $req->validated();

        $data['company_id'] = auth()->user()->company_id;
        $data['employee_id'] = $employee->id;
        $data['created_by'] = auth()->id();

        //   return $req->all();

        // attachments -> Cloudinary (URLs save honge)
        if ($req->hasFile('attachments')) {
            $urls = [];

            foreach ((array) $req->file('attachments') as $file) {
                $isVideo = \Str::startsWith($file->getMimeType(), 'video');

                $res = (new \Cloudinary\Api\Upload\UploadApi())->upload(
    $file->getRealPath(),
    [
        'folder'        => 'coach/' . auth()->user()->company_id . '/' . $employee->id,
        'resource_type' => $isVideo ? 'video' : 'auto',
    ]
);

                // sirf URL save kar rahe hain (simple drop-in replacement)
                // $urls[] = $uploaded->getSecurePath();
                $urls[] = $res['secure_url'];

                // richer: keep public_id for future deletes / transforms
                $details[] = [
                    'url'       => $res['secure_url'],
                    'public_id' => $res['public_id'],
                    'type'      => $isVideo ? 'video' : 'file',
                ];

                // $data['attachments'] = $urls;

                // — Agar aap future me delete/transform karna chahen to iski jagah yeh store karein:
                // $urls[] = [
                //     'url'       => $uploaded->getSecurePath(),
                //     'public_id' => $uploaded->getPublicId(),
                //     'type'      => $isVideo ? 'video' : 'file',
                // ];
            }

            $data['attachments'] = $urls; // JSON column par cast ho to direct array save ho jayega

            // echo "<pre>"; print_r($data); echo "</pre>";
        }
      
        CoachingSession::create($data);

        return redirect()->route('coaching.index', $employee)->with('ok', 'Saved.');
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
