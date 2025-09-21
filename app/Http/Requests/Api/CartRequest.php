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
                'clear_all' => ['required', 'int', 'in:0,1'],
            ];
        }

        if (!in_array($method, ['POST', 'PUT'], true)) {
            return [];
        }

        $rules = [
            'user_id' => ['required', 'int'],
            'tour_id' => ['required', 'int'],
            'customer' => ['required', 'int', 'in:1,2,3'], // 1: Adult, 2: Children, 3: Baby
            'quantity' => ['required', 'int', 'min:1'],
            'price' => ['required', 'numeric'],
        ];

        if ($method === 'PUT') {
            $rules['id'] = ['required', 'int'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [];
    }
}
