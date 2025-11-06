<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\DA;
use App\Models\DCD;
use App\Models\Earning;
use App\Models\Referral;
use App\Models\Scan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class DDSWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_dds_workflow()
    {
        $invoiceResponse = null; // Will be set later

        // Phase 1: User Registrations

        // Register a DA
        $daResponse = $this->postJson('/api/da/create', [
            'name' => 'John DA',
            'email' => 'john.da@example.com',
            'phone' => '+1234567890',
            'password' => 'password123',
            'referrer_code' => 'SYSTEM', // No referrer for first DA
            'national_id' => 'DA123456',
            'dob' => '1990-01-01',
            'gender' => 'male',
            'country' => 'Kenya',
            'county' => 'Nairobi',
            'social_platforms' => ['twitter' => '@johnda'],
            'wallet_pin' => '1234',
            'consent_terms' => true,
            'consent_data' => true,
            'consent_ethics' => true,
        ]);

        $daResponse->assertStatus(201)
            ->assertJsonStructure(['user', 'da']);

        $da = $daResponse->json('da');
        $daUser = $daResponse->json('user');

        // Register a DCD referred by the DA
        $dcdResponse = $this->postJson('/api/dcd/create', [
            'name' => 'Jane DCD',
            'email' => 'jane.dcd@example.com',
            'phone' => '+1234567891',
            'password' => 'password123',
            'da_referral_code' => $da['referral_code'],
            'national_id' => 'DCD123456',
            'dob' => '1985-05-15',
            'gender' => 'female',
            'country' => 'Kenya',
            'business_name' => 'Jane\'s Shop',
            'business_types' => ['retail'],
            'gps_location' => ['lat' => -1.2864, 'lng' => 36.8172],
            'wallet_pin' => '5678',
            'consent_terms' => true,
            'consent_data' => true,
        ]);

        $dcdResponse->assertStatus(201)
            ->assertJsonStructure(['user', 'dcd']);

        $dcd = $dcdResponse->json('dcd');

        // Register a Client
        $clientResponse = $this->postJson('/api/client/create', [
            'name' => 'Bob Client',
            'email' => 'bob.client@example.com',
            'phone' => '+1234567892',
            'password' => 'password123',
            'company_name' => 'Bob\'s Brand',
            'country' => 'Kenya',
            'billing_info' => ['address' => '123 Main St'],
        ]);

        $clientResponse->assertStatus(201)
            ->assertJsonStructure(['user', 'client']);

        $client = $clientResponse->json('client');

        // Phase 2: Campaign Management

        // Create a campaign
        $campaignResponse = $this->postJson('/api/campaign/create', [
            'title' => 'Summer Sale Campaign',
            'description' => 'Promote our summer collection',
            'product_url' => 'https://example.com/summer-sale',
            'budget' => 1000.00,
            'rate_per_scan' => 5.00,
            'target_counties' => ['Nairobi', 'Mombasa'],
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
        ]);

        $campaignResponse->assertStatus(201)
            ->assertJsonStructure(['campaign']);

        $campaign = $campaignResponse->json('campaign');
        $campaignId = $campaign['id']; // Use the actual generated ID

        // Approve the campaign (admin action) - using signed URL
        $approveUrl = URL::signedRoute('api.campaign.approve', ['campaignId' => $campaign['id']]);
        $approveResponse = $this->postJson($approveUrl, [
            'reason' => 'Campaign approved for launch'
        ]);

        $approveResponse->assertStatus(200);

        // Generate invoice after approval
        $invoiceResponse = $this->postJson('/api/invoice/generate', [
            'campaign_id' => $campaignId
        ]);

        $invoiceResponse->assertStatus(200)
            ->assertJsonStructure(['invoice_number', 'download_url']);

        // Mark campaign as paid
        $paidUrl = URL::signedRoute('api.campaign.mark-paid', ['campaignId' => $campaignId]);
        $paidResponse = $this->postJson($paidUrl);

        $paidResponse->assertStatus(200);

        // Phase 3: QR Code Management

        // Generate QR code for DCD
        $qrResponse = $this->postJson('/api/qr/generate-dcd', [
            'dcd_id' => $dcd['id'],
            'campaign_id' => $campaignId
        ]);

        $qrResponse->assertStatus(200)
            ->assertJsonStructure(['qr_code_data', 'download_url']);

        $qrCode = $qrResponse->json('qr_code_data');

        // Validate QR code
        $validateResponse = $this->getJson('/api/scan/validate?qr_code=' . $qrCode);

        $validateResponse->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'campaign_id' => $campaignId,
            ]);

        // Phase 4: Scan Tracking

        // Record a scan
        $scanResponse = $this->postJson('/api/scan/track', [
            'qr_code' => $qrCode
        ]);

        $scanResponse->assertStatus(200)
            ->assertJsonStructure(['scan_id', 'earnings_credited']);

        // Phase 5: Analytics

        // Get campaign analytics
        $analyticsResponse = $this->getJson("/api/scans/analytics/{$campaign['id']}");

        $analyticsResponse->assertStatus(200)
            ->assertJson([
                'campaign_id' => $campaign['id'],
                'total_scans' => 1,
            ]);

        // Phase 6: Earnings & Payouts

        // Calculate earnings
        $earningsResponse = $this->postJson('/api/earnings/calculate', [
            'period' => now()->format('Y-m')
        ]);

        $earningsResponse->assertStatus(200);

        // Generate payout report
        $payoutResponse = $this->getJson('/api/payouts/generate-report');

        $payoutResponse->assertStatus(200)
            ->assertJsonStructure(['total_users', 'total_amount']);

        // Phase 7: Venture Shares

        // Allocate venture shares to DA
        $sharesResponse = $this->postJson('/api/ventureshares/allocate', [
            'user_id' => $daUser['id'],
            'type' => 'da_referral',
            'amount' => 250,
            'reason' => 'DCD referral bonus'
        ]);

        $sharesResponse->assertStatus(200);

        // Phase 8: Admin Functions

        // Get admin digest
        $digestResponse = $this->getJson('/api/admin/digest');

        $digestResponse->assertStatus(200)
            ->assertJsonStructure([
                'user_registrations',
                'campaigns',
                'scans'
            ]);

        // Send admin alert
        $alertResponse = $this->postJson('/api/alerts/send', [
            'type' => 'campaign_submitted',
            'message' => 'New campaign submitted for review',
            'severity' => 'medium',
            'data' => ['campaign_id' => $campaign['id']]
        ]);

        $alertResponse->assertStatus(200);

        // Phase 9: Referral Tracking

        // Track referral event
        $referralResponse = $this->postJson('/api/referral/track', [
            'referrer_id' => $daUser['id'],
            'referee_id' => $dcdResponse->json('user.id'),
            'type' => 'da_to_dcd',
            'event' => 'registration'
        ]);

        $referralResponse->assertStatus(201);

        // Phase 10: Invoice Generation

        // Invoice already generated after approval

        // Verify data integrity
        $this->assertDatabaseHas('users', ['email' => 'john.da@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'jane.dcd@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'bob.client@example.com']);
        $this->assertDatabaseHas('campaigns', ['id' => $campaign['id'], 'status' => 'LIVE']);
        $this->assertDatabaseHas('scans', ['campaign_id' => $campaign['id']]);
        $this->assertDatabaseHas('referrals', ['referrer_id' => $daUser['id']]);

        // Verify file storage
        $downloadUrl = $invoiceResponse->json('download_url');
        $filePath = str_replace('/storage/', '', $downloadUrl); // Remove /storage/ prefix
        $this->assertTrue(Storage::exists($filePath));
    }
}