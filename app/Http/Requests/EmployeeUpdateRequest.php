<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\Employee;

class EmployeeUpdateRequest extends EmployeeStoreRequest
{
    public function rules(): array
    {
        $companyId = $this->user()->company_id ?? null;

        // Route model binding: {employee}
        $employee   = $this->route('employee');
        $employeeId = $employee instanceof Employee
            ? $employee->id
            : (is_numeric($employee) ? (int) $employee : null);

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => [
                'nullable',
                'email',
                'max:191',
                Rule::unique('employees', 'email')
                    ->where(fn ($q) => $q->where('company_id', $companyId))
                    ->ignore($employeeId),
            ],
            'position'   => ['nullable', 'string', 'max:150'],
            'status'     => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }
}
