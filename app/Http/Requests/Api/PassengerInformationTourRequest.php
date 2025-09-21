<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class PassengerInformationTourRequest extends Request
{
    public function rules(): array
    {
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
            'booking_id' => ['required', 'int'],
            'tour_id' => ['required', 'int'],
            'customer' => ['required', 'int', 'in:1,2,3'], // 1: Adult, 2:Children, 3:Baby
            'full_name' => ['required', 'string'],
            'birthday' => ['required', 'date'],
            'gender' => ['required', 'int', 'in:0,1'], // 0: Female, 1: Male
            'card_id' => ['required', 'string','min:12','max:12'],
        ];

        if ($method === 'PUT') {
            $rules['id'] = ['required', 'int', ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'required' => _('Missing_required_parameter'),
        ];
    }
}
