<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\Request;

class CustomerAuthRequest extends Request
{
    public function rules(): array
    {
        $commonRules = [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ];

        return match (true) {
            $this->is('login') && $this->isMethod('POST') => $commonRules,
            $this->is('register') && $this->isMethod('POST') =>
                array_merge($commonRules, ['phone' => ['required', 'string', 'regex:/^(84|0[3|5|7|8|9])[0-9]{8}$/'], 'full_name' => ['required', 'string', 'max:255']]),
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
