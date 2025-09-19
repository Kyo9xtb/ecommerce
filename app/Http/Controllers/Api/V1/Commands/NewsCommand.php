<?php

namespace App\Http\Controllers\Api\V1\Commands;

use AllowDynamicProperties;

#[AllowDynamicProperties] class NewsCommand
{

    public function __construct(public $request)
    {
        $this->id = $request->id;
        $this->title = $request->title;
        $this->meta_title = $request->meta_title;
        $this->slug = $request->slug;
        $this->meta_description = $request->meta_description;
        $this->description = $request->description;
        $this->content = $request->content;
        $this->author = $request->author;
        $this->status = $request->status;
        $this->thumbnail = $request->thumbnail;
    }
}
