<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface MarketDataInterface
{
    /**
     * @param array $symbols Lista de símbolos (ej: ['BTC', 'ETH'])
     * @return Collection<int, \App\DTOs\SecurityPriceData>
     */
    public function fetchPrices(array $symbols): Collection;
}