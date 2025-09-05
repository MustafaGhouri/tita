<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->user()->company_id ?? null;

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['nullable', 'string', 'max:100'],
            // Email is optional… if provided, must be unique within the same company
            'email'      => [
                'nullable',
                'email',
                'max:191',
                Rule::unique('employees', 'email')
                    ->where(fn ($q) => $q->where('company_id', $companyId)),
            ],
            'position'   => ['nullable', 'string', 'max:100'],
            'status'     => ['nullable', Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'email.unique'        => 'This email is already used in your company.',
            'status.in'           => 'Status must be ACTIVE or INACTIVE.',
        ];
    }
}
