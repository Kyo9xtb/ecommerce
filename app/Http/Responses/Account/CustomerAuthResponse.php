<?php

namespace App\Http\Responses\Account;

use Spatie\LaravelData\Data;


class CustomerAuthResponse extends Data
{
    public function __construct(
        public ?string $message,
        public ?array $data = null,
    ) {}
}
