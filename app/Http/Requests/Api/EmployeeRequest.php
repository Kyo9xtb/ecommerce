<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        $commonRules = [
            'full_name' => ['required', 'string'],
            'gender' => ['nullable', 'integer', 'in:0,1,2'], // 0: Nữ, 1: Nam, 2: Other
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'address' => ['nullable', 'string'],
            'position' => ['nullable', 'integer',],
            'department' => ['nullable', 'integer',],
            'password' => ['nullable', 'string', 'min:6'],
            'status' => ['nullable', 'integer', 'in:0,1,2,3,4,5'],
        ];

        return match ($this->method()) {
            'DELETE' => ['id' => ['required', 'integer']],
            'POST'   => $commonRules,
            'PUT'    => array_merge($commonRules, ['id' => ['required', 'integer']], ['employee_code' => ['required', 'string']]),
            default  => [],
        };
    }

    public function messages(): array
    {
        return [
            'required' => __('missing_required_parameter'),
        ];
    }
}
