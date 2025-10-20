<?php

namespace App\Services;

use App\Models\Security;
use App\Models\SecurityType;
use App\Models\SecurityPrice;

/**
 * Service that handles syncing security prices from an external API (mocked)
 */
class SecurityPriceSyncService
{
    /**
     * Sync prices for a given security type
     */
    public function syncByType(string $slug): void
    {
        $type = SecurityType::where('slug', $slug)->firstOrFail();
        $existingSymbols = $type->securities()->pluck('symbol')->toArray();

        // Mock external API response
        $response = $this->mockApiResponse($slug);

        foreach ($response['results'] as $item) {
            if (in_array($item['symbol'], $existingSymbols)) {
                $security = Security::where('symbol', $item['symbol'])->first();

                SecurityPrice::updateOrCreate(
                    ['security_id' => $security->id],
                    [
                        'last_price' => $item['price'],
                        'as_of_date' => $item['last_price_datetime']
                    ]
                );
            }
        }
    }

    /**
     * Simulate an external API response
     */
    protected function mockApiResponse(string $type): array
    {
        return [
            'results' => [
                ['symbol' => 'APPL', 'price' => 188.97, 'last_price_datetime' => now()],
                ['symbol' => 'TSLA', 'price' => 244.42, 'last_price_datetime' => now()],
            ]
        ];
    }
}
