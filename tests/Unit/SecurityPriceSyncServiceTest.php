<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\SecurityPriceSyncService;
use App\Services\MarketDataProviderFactory;
use App\Contracts\MarketDataInterface;
use App\DTOs\SecurityPriceData;
use App\Models\Security;
use App\Models\SecurityType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Mockery;

class SecurityPriceSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_prices_using_the_correct_adapter()
    {
        // 1. ARRANGE (Preparar el escenario)
        
        // Creamos datos base en BD
        $type = SecurityType::create(['slug' => 'crypto', 'name' => 'Cryptocurrency']);
        $security = Security::create([
            'security_type_id' => $type->id,
            'symbol' => 'BTC',
            'name' => 'Bitcoin'
        ]);

        // Mock del Adaptador (El "doble" de riesgo)
        // No nos conectamos a nada real, solo decimos "si te llaman, devuelve esto"
        $mockAdapter = Mockery::mock(MarketDataInterface::class);
        $mockAdapter->shouldReceive('fetchPrices')
            ->once()
            ->with(['BTC']) // Esperamos que pida precio de BTC
            ->andReturn(new Collection([
                new SecurityPriceData(
                    symbol: 'BTC',
                    price: 50000.00,
                    currency: 'USD',
                    timestamp: Carbon::now()
                )
            ]));

        // Mock de la Factory
        $mockFactory = Mockery::mock(MarketDataProviderFactory::class);
        $mockFactory->shouldReceive('make')
            ->with('crypto')
            ->andReturn($mockAdapter);

        // Inyectamos el Mock en el servicio
        $service = new SecurityPriceSyncService($mockFactory);

        // 2. ACT (Acción)
        $service->syncAll();

        // 3. ASSERT (Verificación)
        // Verificamos que se guardó en la base de datos
        $this->assertDatabaseHas('security_prices', [
            'security_id' => $security->id,
            'last_price' => 50000.00
        ]);
    }
}