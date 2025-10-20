<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\Request;

class EmployeeAuthRequest extends Request
{
    public function rules(): array
    {
        $commonRules = [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];

        return match (true) {
            $this->is('login') && $this->isMethod('POST') => $commonRules,
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'required' => __('missing_required_parameter'),
        ];
    }
}
