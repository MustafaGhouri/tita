<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MysteryEvaluationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'checklist_id' => 'required|exists:mystery_checklists,id',
            'monthKey'     => 'required|date_format:Y-m',
            'answers'      => 'required|array',
            'video'        => 'required|file|mimetypes:video/mp4,video/avi,video/quicktime|max:204800', // 200MB
        ];
    }
}
