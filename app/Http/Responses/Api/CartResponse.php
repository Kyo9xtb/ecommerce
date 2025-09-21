<?php

namespace App\Http\Responses\Api;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class CartResponse extends Data
{
    public function __construct(
        public ?string $message,
        public ?array $data = null,

    ) {}
}
