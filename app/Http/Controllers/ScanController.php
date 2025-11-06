<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\Campaign;
use App\Jobs\ProcessScanEarnings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    public function trackApi(Request $request)
    {
        $qrCode = $request->input('qr_code');
        
        if (!$qrCode) {
            return response()->json(['error' => 'QR code is required'], 400);
        }

        try {
            // Same logic as track, but return JSON
            $parsed = $this->parseQRCode($qrCode);

            if (!$parsed) {
                return response()->json(['error' => 'Invalid QR code format'], 400);
            }

            ['dcd_id' => $dcdId, 'campaign_id' => $campaignId] = $parsed;

            $campaign = Cache::remember(
                "campaign:{$campaignId}",
                300,
                fn() => Campaign::where('id', $campaignId)->where('status', 'LIVE')->first()
            );

            if (!$campaign) {
                return response()->json(['error' => 'Campaign not found or inactive'], 404);
            }

            $deviceId = $this->generateDeviceFingerprint($request);

            $isDuplicate = Scan::where('device_id', $deviceId)
                ->where('campaign_id', $campaignId)
                ->where('created_at', '>=', now()->subHour())
                ->exists();

            if ($isDuplicate) {
                return response()->json([
                    'message' => 'Scan recorded (duplicate prevented)',
                    'redirect_url' => $campaign->product_url,
                    'duplicate' => true
                ]);
            }

            $scan = Scan::create([
                'qr_code' => $qrCode,
                'dcd_id' => $dcdId,
                'campaign_id' => $campaignId,
                'device_id' => $deviceId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'earnings_amount' => $campaign->rate_per_scan,
                'location' => $this->getLocationFromIP($request->ip()),
            ]);

            ProcessScanEarnings::dispatch($scan->id)->onQueue('earnings');

            return response()->json([
                'message' => 'Scan recorded successfully',
                'redirect_url' => $campaign->product_url,
                'scan_id' => $scan->id,
                'earnings_credited' => $campaign->rate_per_scan
            ]);

        } catch (\Exception $e) {
            Log::error("Scan API error", [
                'qr_code' => $qrCode,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function validateQr(Request $request)
    {
        $qrCode = $request->query('qr_code');

        if (!$qrCode) {
            return response()->json(['error' => 'QR code parameter is required'], 400);
        }

        $parsed = $this->parseQRCode($qrCode);

        if (!$parsed) {
            \Log::info("QR parsing failed", ['qr_code' => $qrCode]);
            return response()->json([
                'valid' => false,
                'error' => 'Invalid QR code format'
            ], 400);
        }

        ['dcd_id' => $dcdId, 'campaign_id' => $campaignId] = $parsed;

        // Handle pending QR codes (not yet assigned to a campaign)
        if ($campaignId === 'PENDING') {
            return response()->json([
                'valid' => false,
                'error' => 'QR code not yet assigned to an active campaign'
            ], 400);
        }

        $campaign = Campaign::where('id', $campaignId)->where('status', 'LIVE')->first();

        if (!$campaign) {
            // Debug: check all campaigns
            $allCampaigns = Campaign::all();
            \Log::info("All campaigns in validation", [
                'campaigns' => $allCampaigns->pluck('id', 'status')->toArray(),
                'looking_for' => $campaignId,
                'parsed' => $parsed
            ]);
            return response()->json([
                'valid' => false,
                'error' => 'Campaign not found or inactive'
            ], 404);
        }

        return response()->json([
            'valid' => true,
            'campaign_id' => $campaignId,
            'dcd_id' => $dcdId,
            'product_url' => $campaign->product_url,
            'campaign_title' => $campaign->title,
            'rate_per_scan' => $campaign->rate_per_scan,
        ]);
    }

    public function analytics(Request $request, $campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);

        // Get scan statistics
        $totalScans = Scan::where('campaign_id', $campaignId)->count();
        $uniqueDevices = Scan::where('campaign_id', $campaignId)->distinct('device_id')->count('device_id');
        $totalEarnings = Scan::where('campaign_id', $campaignId)->sum('earnings_amount');

        // Get daily scan counts for the last 30 days
        $dailyScans = Scan::where('campaign_id', $campaignId)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get top performing DCDs
        $topDcds = Scan::where('campaign_id', $campaignId)
            ->selectRaw('dcd_id, COUNT(*) as scan_count, SUM(earnings_amount) as total_earnings')
            ->groupBy('dcd_id')
            ->orderByDesc('scan_count')
            ->limit(10)
            ->get();

        return response()->json([
            'campaign_id' => $campaignId,
            'campaign_title' => $campaign->title,
            'total_scans' => $totalScans,
            'unique_devices' => $uniqueDevices,
            'total_earnings' => $totalEarnings,
            'daily_scans' => $dailyScans,
            'top_dcds' => $topDcds,
            'conversion_rate' => $totalScans > 0 ? round(($uniqueDevices / $totalScans) * 100, 2) : 0,
        ]);
    }

    /**
     * Parse QR code format: DCD_{dcd_id}_CAMP_{campaign_id}
     */
    private function parseQRCode(string $qrCode): ?array
    {
        // Remove any URL parts if full URL was scanned
        $qrCode = basename($qrCode);

        // Expected format: DCD_DCD_{dcd_suffix}_CAMP_CAMP_{campaign_suffix}
        // Example: DCD_DCD_E3CE77_CAMP_CAMP_690C8DEE4CFA4
        if (!preg_match('/^DCD_(DCD_[A-Z0-9]+)_CAMP_(CAMP_[A-Z0-9_]+)$/', $qrCode, $matches)) {
            return null;
        }

        return [
            'dcd_id' => $matches[1], // DCD_E3CE77
            'campaign_id' => $matches[2], // CAMP_690C8DEE4CFA4
        ];
    }

    /**
     * Generate device fingerprint for fraud detection
     */
    private function generateDeviceFingerprint(Request $request): string
    {
        $factors = [
            $request->ip(),
            $request->userAgent(),
            $request->header('Accept-Language'),
        ];

        return hash('sha256', implode('|', $factors));
    }

    /**
     * Get approximate location from IP address
     */
    private function getLocationFromIP(string $ip): ?array
    {
        // Placeholder: integrate with ip-api.com or similar
        // For now, return null
        return null;
    }
}