<?php

namespace App\Integrations;

use App\Contracts\MarketDataInterface;
use App\DTOs\SecurityPriceData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class FrankfurterAdapter implements MarketDataInterface
{
    public function fetchPrices(array $symbols): Collection
    {
        // Frankfurter requiere 'from' y 'to'. Usamos USD como base.
        $response = Http::timeout(10)->get("https://api.frankfurter.app/latest", [
            'from' => 'USD',
            'to' => implode(',', $symbols)
        ]);

        if ($response->failed()) {
            return collect();
        }

        $data = $response->json();
        $rates = $data['rates'] ?? [];
        $date = $data['date'] ?? now();

        $collection = collect();

        foreach ($rates as $symbol => $price) {
            $collection->push(new SecurityPriceData(
                symbol: $symbol,
                price: (float) $price, // Ojo: Esto es cuánto vale 1 USD en esa moneda
                currency: $data['base'], // USD
                timestamp: Carbon::parse($date)
            ));
        }

        return $collection;
    }
}