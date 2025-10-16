<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class CustomerRequest extends Request
{
    public function rules(): array
    {
        $commonRules = [
            'full_name' => ['required', 'string'],
            'gender' => ['nullable', 'integer', 'in:0,1,2'], // 0: Nữ, 1: Nam, 2: Other
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'address' => ['nullable', 'string'],
            'card_id' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:6'],
            'status' => ['nullable', 'int', 'in:0,1,2,3,4,5'],
        ];

        return match ($this->method()) {
            'DELETE' => ['id' => ['required', 'integer']],
            'POST'   => $commonRules,
            'PUT'    => array_merge($commonRules, ['id' => ['required', 'integer']], ['customer_code' => ['required', 'string']]),
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
