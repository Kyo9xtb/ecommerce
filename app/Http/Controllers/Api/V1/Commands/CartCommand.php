<?php

namespace App\Http\Controllers\Api\V1\Commands;

use AllowDynamicProperties;

#[AllowDynamicProperties] class CartCommand
{
    public function __construct(public $request)
    {
        $this->id = $request->id;
        $this->user_id = $request->user_id;
        $this->total_amount = $request->total_amount;
        $this->status = $request->status;
        $this->details = $request->details;
        $this->clear_all = $request->clear_all;
    }
}
