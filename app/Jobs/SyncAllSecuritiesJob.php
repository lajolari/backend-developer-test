<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\SecurityPriceSyncService;
use App\Models\SecurityType;

class SyncAllSecuritiesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SecurityPriceSyncService $syncService): void
    {
        foreach (SecurityType::all() as $type) {
            $syncService->syncByType($type->slug);
        }
    }
}
