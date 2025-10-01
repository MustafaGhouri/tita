<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// app/Http/Requests/CoachingSessionRequest.php
class CoachingSessionRequest extends FormRequest {
    public function authorize(){ return auth()->user()->isClient(); }
    public function rules(){
       return [
        'date'            => ['nullable', 'date'],
        'summary'         => ['nullable', 'string', 'max:2000'],
        'follow_up_date'  => ['nullable', 'date'],
        // multiple files allowed: attachments[]
        'attachments'     => ['sometimes', 'array'],
        'attachments.*'   => [
            'file',
            // Either mimes OR mimetypes; mimes is simpler:
            'mimes:mp4,mov,avi,mkv,wmv,webm,pdf,jpg,jpeg,png',
            // size in KB; 204800 = 200MB (adjust as needed)
            'max:204800'
        ],
    ];
    }
}

