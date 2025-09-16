<?php

namespace App\Http\Responses\Api;

use Spatie\LaravelData\Data;

class TourRequireResponse extends Data
{
    public function __construct(
        public ?string $message,
        public ?array $data = null,
    ) {}
}
