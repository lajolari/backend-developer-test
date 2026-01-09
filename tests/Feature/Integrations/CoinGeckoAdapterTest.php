<?php

namespace Tests\Feature\Integrations;

use Tests\TestCase;
use App\Integrations\CoinGeckoAdapter;
use App\DTOs\SecurityPriceData;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CoinGeckoAdapterTest extends TestCase
{
    public function test_it_fetches_and_normalizes_prices_from_coingecko()
    {
        // 1. ARRANGE
        // Simulamos la respuesta exacta que devuelve CoinGecko
        Http::fake([
            'api.coingecko.com/*' => Http::response([
                'bitcoin' => [
                    'usd' => 45000.50,
                    'last_updated_at' => 1635600000 // Timestamp Unix
                ]
            ], 200),
        ]);

        $adapter = new CoinGeckoAdapter();

        // 2. ACT
        // Pedimos 'bitcoin'. El adapter debe manejar la conversión a minúsculas si es necesario
        $results = $adapter->fetchPrices(['bitcoin']);

        // 3. ASSERT
        $this->assertCount(1, $results);
        $dto = $results->first();

        // Verificamos que sea un DTO válido y los datos sean correctos
        $this->assertInstanceOf(SecurityPriceData::class, $dto);
        $this->assertEquals('BITCOIN', $dto->symbol); // Asumimos que lo normaliza a mayúsculas
        $this->assertEquals(45000.50, $dto->price);
        $this->assertEquals('USD', $dto->currency);
    }

    public function test_it_handles_api_failures_gracefully()
    {
        // Simulamos error 500
        Http::fake([
            '*' => Http::response(null, 500),
        ]);

        $adapter = new CoinGeckoAdapter();
        $results = $adapter->fetchPrices(['bitcoin']);

        // Debe devolver colección vacía, no explotar
        $this->assertCount(0, $results);
    }
}