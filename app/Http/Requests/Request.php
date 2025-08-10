<?php

namespace App\Http\Requests;

use App\Enum\ResponseStatusCode;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\JsonApiException;

class Request extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new JsonApiException($validator->messages()->first(), ResponseStatusCode::PARAMS_INVALID->value);
    }
}
