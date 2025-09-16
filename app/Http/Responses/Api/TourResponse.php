<?php

namespace App\Http\Responses\Api;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class TourResponse extends Data
{
    public function __construct(
        public ?string $message,
        public DataCollection|array|null $data = null,

    ) {}
}
