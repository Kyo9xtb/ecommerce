<?php

namespace App\Http\Controllers\Api\V1\Commands;

use AllowDynamicProperties;

#[AllowDynamicProperties] class TourCommand
{

    public function __construct(public $request)
    {
        $this->id = $request->id;
        $this->tour_code = $request->tour_code;
        $this->tour_name = $request->tour_name;
        $this->slug = $request->slug;
        $this->price = $request->price;
        $this->sale = $request->sale;
        $this->trip = $request->trip;
        $this->time = $request->time;
        $this->status = $request->status;
        $this->tour_summary = $request->tour_summary;
        $this->tour_program = $request->tour_program;
        $this->tour_policy = $request->tour_policy;
        $this->terms_conditions = $request->terms_conditions;
        $this->images = $request->images;
        $this->thumbnail = $request->thumbnail;
        $this->vehicles = $request->vehicles;
        $this->area = $request->area;
        $this->tour_group = $request->tour_group;
        $this->departure_schedule = $request->departure_schedule;
        $this->guests = $request->guests;
    }
}
