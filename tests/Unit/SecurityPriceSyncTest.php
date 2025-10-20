<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\SecurityType;
use App\Models\Security;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityPriceSyncTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_syncs_prices_for_a_given_security_type()
    {
        $type = SecurityType::factory()->create(['slug' => 'mutual_funds']);
        Security::factory()->create(['security_type_id' => $type->id, 'symbol' => 'APPL']);

        $response = $this->postJson('/api/securities/sync', [
            'security_type' => 'mutual_funds',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'success']);
    }

    /** @test */
    public function it_returns_422_if_security_type_is_invalid()
    {
        $response = $this->postJson('/api/securities/sync', [
            'security_type' => 'invalid_type',
        ]);

        $response->assertStatus(422);
    }
}
