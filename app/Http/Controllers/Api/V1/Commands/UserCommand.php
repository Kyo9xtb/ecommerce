<?php

namespace App\Http\Controllers\Api\V1\Commands;

class UserCommand
{
    // public int $ApiId;

    public function __construct(public $request)
    {
        // $this->ApiId = $request->AppId;
    }
}
