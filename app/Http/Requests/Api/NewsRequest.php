<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class NewsRequest extends Request
{
    public function rules(): array
    {
        return match ($this->method()) {
            'DELETE' => [
                'id' => ['required', 'integer'],
            ],

            'POST', 'PUT' => array_merge(
                [
                    'title'            => ['required', 'string'],
                    'meta_title'       => ['required', 'string'],
                    'slug'             => ['required', 'string'],
                    'meta_description' => ['required', 'string'],
                    'description'      => ['nullable', 'string'],
                    'content'          => ['required', 'string'],
                    'author'           => ['required', 'string'],
                    'status'           => ['nullable', 'integer', 'in:0,1'], // 0: Inactive, 1: Active
                ],
                $this->isMethod('PUT') ? ['id' => ['required', 'integer']] : []
            ),

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
