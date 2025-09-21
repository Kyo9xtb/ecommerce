<?php

namespace App\Http\Controllers\Api\V1\Commands;

use AllowDynamicProperties;

#[AllowDynamicProperties] class CartCommand
{
    public function __construct(public $request)
    {
        $this->id = $request->id;
        $this->user_id = $request->user_id;
        $this->tour_id = $request->tour_id;
        $this->customer = $request->customer;
        $this->quantity = $request->quantity;
        $this->price = $request->price;
        $this->clear_all = $request->clear_all;
    }
}
