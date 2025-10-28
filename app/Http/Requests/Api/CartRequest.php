<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class CartRequest extends Request
{
    public function rules(): array
    {
        $method = request()->method();

        if ($method === 'DELETE') {
            return [
                'id' => ['required', 'int'],
                'user_id' => ['nullable', 'int'],
                'clear_all' => ['nullable', 'int', 'in:0,1'],
            ];
        }

        if (!in_array($method, ['POST', 'PUT'], true)) {
            return [];
        }

        $rules = [
            'user_id' => ['required', 'int'],
            'total_amount' => ['required', 'integer'],
            'status' => ['nullable', 'integer', 'in:1,2,3'], // 1: Adult, 2: Children, 3: Baby
        ];

        if ($method === 'PUT') {
            $rules['id'] = ['required', 'integer'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [];
    }
}
