<?php

namespace App\Http\Controllers\Api\V1\Commands;

use AllowDynamicProperties;

#[AllowDynamicProperties] class CustomerCommand
{

    public function __construct(public $request)
    {
        $this->id = $request->id;
        $this->customer_code = $request->customer_code;
        $this->email = $request->email;
        $this->password = $request->password;
        $this->full_name = $request->full_name;
        $this->birth_date = $request->birth_date;
        $this->gender = $request->gender;
        $this->email = $request->email;
        $this->phone = $request->phone;
        $this->address = $request->address;
        $this->card_id = $request->card_id;
        $this->status = $request->status;
    }
}
