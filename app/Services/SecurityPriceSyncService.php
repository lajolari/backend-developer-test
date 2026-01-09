<?php

namespace App\Services;

use App\Models\Security;
use App\Models\SecurityPrice;
use App\Models\SecurityType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SecurityPriceSyncService
{
    public function __construct(
        protected MarketDataProviderFactory $factory
    ) {}

    public function syncAll(): void
    {
        // 1. Obtenemos todos los tipos (Crypto, Forex, Stocks)
        $types = SecurityType::with('securities')->get();

        foreach ($types as $type) {
            try {
                // 2. La Factory nos da el adaptador correcto (Estrategia)
                $adapter = $this->factory->make($type->slug);

                $securities = $type->securities;
                if ($securities->isEmpty()) continue;

                $symbols = $securities->pluck('symbol')->toArray();

                // 3. Obtenemos datos normalizados (DTOs)
                $pricesDto = $adapter->fetchPrices($symbols);

                // 4. Guardamos masivamente (Batch Insert)
                DB::transaction(function () use ($pricesDto, $securities) {
                    foreach ($pricesDto as $dto) {
                        $security = $securities->firstWhere('symbol', $dto->symbol);
                        
                        if ($security) {
                            SecurityPrice::create([
                                'security_id' => $security->id,
                                'last_price'  => $dto->price,
                                'currency'    => $dto->currency,
                                'as_of_date'  => $dto->timestamp,
                            ]);
                        }
                    }
                });

                Log::info("Synced {$pricesDto->count()} prices for {$type->slug}");

            } catch (\Exception $e) {
                // Si falla un tipo (ej: API caída), logueamos y seguimos con el siguiente
                Log::error("Sync failed for {$type->slug}: " . $e->getMessage());
            }
        }
    }
}