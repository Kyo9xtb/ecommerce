<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class BookingTourRequest extends Request
{
    public function rules(): array
    {
        // return [];
        $method = request()->method();

        if ($method === 'DELETE') {
            return [
                'id' => ['required', 'int'],
            ];
        }

        if (!in_array($method, ['POST', 'PUT'], true)) {
            return [];
        }

        $rules = [
            'user_id' => ['nullable', 'int'],
            'full_name' => ['required', 'string'],
            'email' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'address' => ['required', 'string'],
            'total_price' => ['required', 'numeric'],
            'deposit' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string'],
        ];

        if ($method === 'PUT') {
            $rules['id'] = ['required', 'int'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'full_name.required' => __('full_name.missing_required_parameter'),
            'email.required' => __('email.missing_required_parameter'),
            'phone.required' => __('phone.missing_required_parameter'),
            'address.required' => __('address.missing_required_parameter'),
            'total_price.required' => __('total_price.missing_required_parameter'),
        ];
    }
}
