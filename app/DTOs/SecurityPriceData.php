<?php

namespace App\DTOs;

use Carbon\Carbon;

readonly class SecurityPriceData
{
    public function __construct(
        public string $symbol,
        public float $price,
        public string $currency,
        public Carbon $timestamp
    ) {}
}