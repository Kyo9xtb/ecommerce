<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class ContactCustomerRequest extends Request
{
    public function rules(): array
    {
        $commonRules = [
            'full_name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'title' => ['required', 'string'],
            'contact_content' => ['required', 'string'],
            'contact_result' => ['nullable', 'string'],
            'status' => ['nullable', 'int', 'in:0,1,2,3,4,5'],
        ];

        return match ($this->method()) {
            'DELETE' => ['id' => ['required', 'integer']],
            'POST'   => $commonRules,
            'PUT'    => array_merge($commonRules, ['id' => ['required', 'integer']], ['contact_code' => ['required', 'string']]),
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
