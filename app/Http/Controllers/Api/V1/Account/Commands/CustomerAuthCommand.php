<?php

namespace App\Http\Controllers\Api\V1\Account\Commands;

use AllowDynamicProperties;

#[AllowDynamicProperties] class CustomerAuthCommand
{

    public function __construct(public $request)
    {
        $this->email = $request->email;
        $this->password = $request->password;
    }
}
