<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// app/Http/Requests/DiagnosticSubmitRequest.php
class DiagnosticSubmitRequest extends FormRequest {
    public function authorize(){
        $user = auth()->user();
        return $user->isClient() || $user->isEmployee(); // further controller guards
    }
    public function rules(){ return ['answers'=>'required|array','manual_score'=>'nullable|numeric|min:0|max:100']; }
}

