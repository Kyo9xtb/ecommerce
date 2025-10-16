<?php

namespace App\Http\Controllers\Api\V1\Commands;

use AllowDynamicProperties;

#[AllowDynamicProperties] class EmployeeCommand
{

    public function __construct(public $request)
    {
        $this->id = $request->id;
        $this->employee_code = $request->employee_code;
        $this->email = $request->email;
        $this->password = $request->password;
        $this->full_name = $request->full_name;
        $this->birth_date = $request->birth_date;
        $this->gender = $request->gender;
        $this->email = $request->email;
        $this->phone = $request->phone;
        $this->address = $request->address;
        $this->position = $request->position;
        $this->department = $request->department;
        $this->status = $request->status;
    }
}
