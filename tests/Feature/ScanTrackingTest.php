<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Campaign;
use App\Models\DCD;
use App\Models\Earning;
use App\Models\Scan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScanTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_scan_redirects_to_product_url()
    {
        $campaign = Campaign::factory()->create([
            'id' => 'CAMP_TEST001',
            'product_url' => 'https://example.com/product',
            'status' => 'LIVE',
            'rate_per_scan' => 10,
        ]);

        $dcd = DCD::factory()->create(['id' => 'DCD_DCD001']);

        $response = $this->postJson('/api/scan/track', ['qr_code' => 'DCD_DCD_DCD001_CAMP_CAMP_TEST001']);

        $response->assertStatus(200)
            ->assertJson([
                'redirect_url' => 'https://example.com/product',
                'earnings_credited' => 10,
            ]);
        $this->assertDatabaseHas('scans', [
            'campaign_id' => 'CAMP_TEST001',
            'dcd_id' => 'DCD_DCD001',
            'earnings_amount' => 10,
        ]);
    }

    public function test_duplicate_scan_prevented()
    {
        // Create campaign and DCD
        $campaign = Campaign::factory()->create([
            'id' => 'CAMP_TEST001',
            'product_url' => 'https://example.com/product',
            'status' => 'LIVE',
        ]);
        $dcd = DCD::factory()->create(['id' => 'DCD_DCD001']);

        // First scan
        $this->postJson('/api/scan/track', ['qr_code' => 'DCD_DCD_DCD001_CAMP_CAMP_TEST001']);

        // Second scan (same device, within 1 hour)
        $this->postJson('/api/scan/track', ['qr_code' => 'DCD_DCD_DCD001_CAMP_CAMP_TEST001']);

        // Should only have 1 scan recorded
        $this->assertEquals(1, Scan::count());
    }

    public function test_invalid_qr_code_returns_error()
    {
        $response = $this->postJson('/api/scan/track', ['qr_code' => 'INVALID_QR']);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Invalid QR code format']);
    }

    public function test_inactive_campaign_returns_error()
    {
        $campaign = Campaign::factory()->create([
            'id' => 'CAMP_TEST001',
            'status' => 'COMPLETED', // Not LIVE
        ]);
        $dcd = DCD::factory()->create(['id' => 'DCD_DCD001']);

        $response = $this->postJson('/api/scan/track', ['qr_code' => 'DCD_DCD_DCD001_CAMP_CAMP_TEST001']);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Campaign not found or inactive']);
    }

    public function test_payout_report_generation()
    {
        // Create test earnings
        $user = User::factory()->create();
        $scan = Scan::factory()->create(['earnings_amount' => 10.00]);
        
        Earning::create([
            'user_id' => $user->id,
            'scan_id' => $scan->id,
            'amount' => 10.00,
            'type' => 'scan',
            'status' => 'pending',
            'period' => now()->subMonth()->format('Y-m'), // Use last month
        ]);

        $response = $this->getJson('/api/payouts/generate-report');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Payout report generated',
                'total_users' => 1,
                'total_amount' => 10.00,
            ]);
    }
}