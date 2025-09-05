<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// app/Http/Requests/CoachingSessionRequest.php
class CoachingSessionRequest extends FormRequest {
    public function authorize(){ return auth()->user()->isClient(); }
    public function rules(){
        return [
            'employee_id'   =>'nullable|exists:employees,id',
            'date'          =>'required|date',
            'observations'  =>'nullable',
            'recommendations'=>'nullable',
            'follow_up_date'=>'nullable|date|after_or_equal:date',
            'attachments.*' =>'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ];
    }
}

