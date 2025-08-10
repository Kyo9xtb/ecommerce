<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class JsonApiData extends Data
{
    public function __construct(
        public ?string $status,
        public ?string $message,
        public ?int $error_code,
        public ?array $data,
    ) {}
}
