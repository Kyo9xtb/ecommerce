<?php

namespace App\Http\Controllers\Api\V1\Commands;

use AllowDynamicProperties;

#[AllowDynamicProperties] class BookingTourCommand
{

    public function __construct(public $request)
    {
        $this->id = $request->id;
        $this->user_id = $request->user_id;
        $this->full_name = $request->full_name;
        $this->email = $request->email;
        $this->phone = $request->phone;
        $this->currency = $request->currency;
        $this->address = $request->address;
        $this->note = $request->note;
        $this->total_price = $request->total_price;
        $this->deposit = $request->deposit;
        $this->payment_method = $request->payment_method;
        $this->status = $request->status;
        $this->details = $request->details;
        $this->date = $request->date;
        $this->month = $request->month;
        $this->report = $request->report;
        $this->booking_code = $request->booking_code;
    }
}
