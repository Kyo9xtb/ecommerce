<?php

namespace App\Exceptions;

use App\Trait\ResponseJson;
use Exception;

class JsonApiException extends Exception
{
    use ResponseJson;

    /**
     * @param string $msg
     * @param int $errorCode
     * @param null $data
     * @param array|null $dataExtra
     */
    public function __construct(
        public string $msg,
        public int $errorCode,
        public $data = null,
        public array|null $dataExtra = null,
    ) {}

    public function render(): \Illuminate\Http\JsonResponse
    {
        return $this->responseFail($this->msg, $this->errorCode, $this->data, $this->dataExtra);
    }
}
