<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class TourRequest extends Request
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
            'tour_name' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'area' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'sale' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'trip' => ['nullable', 'string'],
            'time' => ['nullable', 'string'],
            'tour_summary' => ['nullable', 'string'],
            'tour_program' => ['nullable', 'string'],
            'tour_policy' => ['nullable', 'string'],
            'terms_conditions' => ['nullable', 'string'],
            'status' => ['nullable', 'int', 'in:0,1'], // 0: Inactive, 1: Active
            'tour_group' => ['required', 'int', 'in:1,2'], // 1: Domestic, 2: International
        ];

        if ($method === 'PUT') {
            $rules['id'] = ['required', 'int'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'tour_name.required' => __('tour_name.missing_required_parameter'),
            'slug.required' => __('slug.missing_required_parameter'),
            'price.required' => __('price.missing_required_parameter'),
            'tour_group.required' => __('tour_group.missing_required_parameter'),
            'area.required' => __('area.missing_required_parameter'),

            'price.numeric' => __('price_must_be_numeric'),
            'sale.numeric' => __('sale_must_be_numeric'),

            'trip.string' => __('trip_must_be_string'),
            'time.string' => __('time_must_be_string'),

            'tour_summary.string' => __('tour_summary_must_be_string'),
            'tour_program.string' => __('tour_program_must_be_string'),
            'tour_policy.string' => __('tour_policy_must_be_string'),
            'terms_conditions.string' => __('terms_conditions_must_be_string'),

            'status.int' => __('status_must_be_integer'),
            'status.in' => __('status_must_be_0_or_1'),
            'tour_group.in' => __('status_must_be_1_or_2'),
        ];
    }
}
