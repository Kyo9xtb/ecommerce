<?php

namespace App\Http\Controllers\Api\V1\Commands;

class ApiCommand
{
    public int $ApiId;

    public function __construct(public $request)
    {
        $this->ApiId = $request->AppId;
    }
}
