<?php

namespace App\Http\Controllers\Api\V1\Commands;

use AllowDynamicProperties;

#[AllowDynamicProperties] class PassengerInformationTourCommand
{
    public function __construct(public $request)
    {
        $this->id = $request->id;
        $this->booking_id = $request->booking_id;
        $this->tour_id = $request->tour_id;
        $this->customer = $request->customer;
        $this->full_name = $request->full_name;
        $this->birthday = $request->birthday;
        $this->gender = $request->gender;
        $this->card_id = $request->card_id;
    }
}
