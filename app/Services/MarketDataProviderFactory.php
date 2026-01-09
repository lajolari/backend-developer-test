<?php

namespace App\Services;

use App\Contracts\MarketDataInterface;
use App\Integrations\CoinGeckoAdapter;
use App\Integrations\FrankfurterAdapter;
use InvalidArgumentException;

class MarketDataProviderFactory
{
    public function make(string $securityTypeSlug): MarketDataInterface
    {
        return match ($securityTypeSlug) {
            'cryptocurrency', 'crypto' => app(CoinGeckoAdapter::class),
            'forex', 'currency', 'mutual_funds' => app(FrankfurterAdapter::class), // Usamos Frankfurter como fallback para funds por ahora
            default => throw new InvalidArgumentException("No adapter found for type: {$securityTypeSlug}"),
        };
    }
}