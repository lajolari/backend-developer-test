<?php

namespace App\Http\Controllers;

use App\Services\SecurityPriceSyncService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Controller responsible for triggering price sync for a given security type
 */
class SecurityPriceController extends Controller
{
    public function __construct(private SecurityPriceSyncService $syncService) {}

    public function sync(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'security_type' => 'required|string|exists:security_types,slug',
            ]);

            // Perform the sync process
            $this->syncService->syncByType($validated['security_type']);

            return response()->json([
                'status' => 'success',
                'message' => "Security prices for '{$validated['security_type']}' synced successfully.",
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            Log::error('Price sync failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred during sync.',
            ], 500);
        }
    }
}
