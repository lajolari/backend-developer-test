<?php

namespace App\Integrations;

use App\Contracts\MarketDataInterface;
use App\DTOs\SecurityPriceData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CoinGeckoAdapter implements MarketDataInterface
{
    public function fetchPrices(array $symbols): Collection
    {
        // CoinGecko usa IDs en minúsculas. Asumimos que los símbolos vienen correctos o los mapeamos.
        // En un caso real, tendrías un campo 'api_id' en tu tabla securities.
        $ids = implode(',', array_map('strtolower', $symbols));
        
        $response = Http::timeout(10)->get("https://api.coingecko.com/api/v3/simple/price", [
            'ids' => $ids,
            'vs_currencies' => 'usd',
            'include_last_updated_at' => 'true'
        ]);

        if ($response->failed()) {
            // Aquí podrías lanzar una excepción personalizada
            return collect(); 
        }

        $data = $response->json();
        $collection = collect();

        foreach ($data as $id => $details) {
            $collection->push(new SecurityPriceData(
                symbol: strtoupper($id),
                price: (float) $details['usd'],
                currency: 'USD',
                timestamp: Carbon::createFromTimestamp($details['last_updated_at'])
            ));
        }

        return $collection;
    }
}