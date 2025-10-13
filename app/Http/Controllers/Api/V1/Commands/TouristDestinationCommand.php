<?php

namespace App\Http\Controllers\Api\V1\Commands;

use AllowDynamicProperties;

#[AllowDynamicProperties] class TouristDestinationCommand
{

    public function __construct(public $request)
    {
        $this->id = $request->id;
        $this->place_name = $request->place_name;
        $this->meta_title = $request->meta_title;
        $this->slug = $request->slug;
        $this->description = $request->description;
        $this->details = $request->details;
        $this->tour_group = $request->tour_group;
        $this->area = $request->area;
        $this->status = $request->status;
        $this->existing_thumbnail = $request->existing_thumbnail;
        $this->existing_images = $request->existing_images;
    }
}
