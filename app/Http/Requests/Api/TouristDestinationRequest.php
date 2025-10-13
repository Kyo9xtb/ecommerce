<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class TouristDestinationRequest extends Request
{
    public function rules(): array
    {
        $commonRules = [
            'place_name' => ['required', 'string', 'max:500'],
            'meta_title' => ['required', 'string', 'max:500'],
            'slug' => ['required', 'string'],
            'description' => ['required', 'string', 'max:500'],
            'details' => ['required', 'string'],
            'tour_group' => ['required', 'integer', 'int: 1,2,3,4'],
            'area' => ['required', 'integer'],
            'status' => ['nullable', 'integer', 'in:0,1'],
            'thumbnail' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:1024'], //1MB
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'] //5MB
        ];

        return match ($this->method()) {
            'DELETE' => ['id' => ['required', 'integer']],
            'POST'   => $commonRules,
            'PUT'    => array_merge($commonRules, ['id' => ['required', 'integer'], 'existing_images' => ['nullable', 'array'], 'existing_thumbnail' => ['nullable', 'string'],]),
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
