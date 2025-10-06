<?php

namespace App\Http\Controllers\Api\V1\Commands;

use AllowDynamicProperties;

#[AllowDynamicProperties] class TourRequireCommand
{

    public function __construct(public $request)
    {
        $this->id = $request->id;
        $this->full_name = $request->full_name;
        $this->nationality = $request->nationality;
        $this->email = $request->email;
        $this->phone = $request->phone;
        $this->tour_dates = $request->tour_dates;
        $this->expected_destination = $request->expected_destination;
        $this->departure_date = $request->departure_date;
        $this->status = $request->status;
        $this->end_date = $request->end_date;
        $this->expected_month = $request->expected_month;
        $this->expected_year = $request->expected_year;
        $this->number_days = $request->number_days;
        $this->vehicle = $request->vehicle;
        $this->adult = $request->adult;
        $this->children = $request->children;
        $this->baby = $request->baby;
        $this->number_rooms = $request->number_rooms;
        $this->hotel_standards = $request->hotel_standards;
        $this->note = $request->note;
        $this->feedback = $request->feedback;
        $this->price = $request->price;
        $this->tour_program = $request->tour_program;
        $this->tour_policy = $request->tour_policy;
        $this->terms_conditions = $request->terms_conditions;
    }
}
