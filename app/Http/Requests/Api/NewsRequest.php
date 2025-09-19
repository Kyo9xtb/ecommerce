<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class NewsRequest extends Request
{
    public function rules(): array
    {
        return [];

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
            'title' => ['required', 'string'],
            'meta_title' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'meta_description' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'author' => ['required', 'string'],
            'status' => ['nullable', 'int', 'in:0,1'], // 0: Inactive, 1: Active
        ];

        if ($method === 'PUT') {
            $rules['id'] = ['required', 'int'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'required' => __('missing_required_parameter'),
        ];
    }
}
