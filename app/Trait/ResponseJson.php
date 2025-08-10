<?php

namespace App\Trait;

use App\Enum\ResponseStatus;
use App\Enum\ResponseStatusCode;
use Illuminate\Http\JsonResponse;

trait ResponseJson
{

    public function responseSuccess($data = [], $message = '', $dataExtra = []): JsonResponse
    {
        return $this->responseJson(ResponseStatus::SUCCESS, $message, $data, ResponseStatusCode::NONE_ERR, ResponseStatusCode::SUCCESS, $dataExtra);
    }

    public function responseFail($message = null, $errorCode = 1, $data = [], $dataExtra = []): JsonResponse
    {
        return $this->responseJson(ResponseStatus::FAIL, $message, $data, $errorCode, ResponseStatusCode::SUCCESS, $dataExtra);
    }

    public function responseJson($status, $message, $data, $errorCode, $code = 200, $dataMerge = []): JsonResponse
    {
        $res = [
            'status' => $status,
            'error_code' => $errorCode,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($dataMerge))
            $res = array_merge($res, $dataMerge);

        return new JsonResponse($res, $code);
    }
}
