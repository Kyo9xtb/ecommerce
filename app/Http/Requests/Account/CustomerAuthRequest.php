<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\Request;

class CustomerAuthRequest extends Request
{
    public function rules(): array
    {
        return [
            // 'AppId' => ['required', 'int'],
        ];
    }

    public function messages(): array
    {
        return [
            // 'AppId.required' => 'A AppId is required',
            // 'AppId.int' => 'A AppId is int',
        ];
    }
}
