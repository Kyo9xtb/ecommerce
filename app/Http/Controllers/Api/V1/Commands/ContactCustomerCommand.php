<?php

namespace App\Http\Controllers\Api\V1\Commands;

use AllowDynamicProperties;

#[AllowDynamicProperties] class ContactCustomerCommand
{

    public function __construct(public $request)
    {
        $this->id = $request->id;
        $this->contact_code = $request->contact_code;
        $this->full_name = $request->full_name;
        $this->email = $request->email;
        $this->phone = $request->phone;
        $this->contact_content = $request->contact_content;
        $this->contact_result = $request->contact_result;
        $this->status = $request->status;
        $this->title = $request->title;
    }
}
