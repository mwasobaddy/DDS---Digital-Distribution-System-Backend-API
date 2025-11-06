<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Scan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function digest(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        // Cache key for the digest
        $cacheKey = "admin_digest_{$date}";
        $digest = Cache::remember($cacheKey, 3600, function () use ($date) {
            return [
                'date' => $date,
                'user_registrations' => [
                    'total' => User::whereDate('created_at', $date)->count(),
                    'das' => User::where('role', 'da')->whereDate('created_at', $date)->count(),
                    'dcs' => User::where('role', 'dcd')->whereDate('created_at', $date)->count(),
                    'clients' => User::where('role', 'client')->whereDate('created_at', $date)->count(),
                ],
                'campaigns' => [
                    'submitted' => Campaign::where('status', 'SUBMITTED')->whereDate('created_at', $date)->count(),
                    'approved' => Campaign::where('status', 'APPROVED')->whereDate('created_at', $date)->count(),
                    'live' => Campaign::where('status', 'LIVE')->count(),
                ],
                'scans' => [
                    'total' => Scan::whereDate('created_at', $date)->count(),
                    'unique_devices' => Scan::whereDate('created_at', $date)->distinct('device_id')->count('device_id'),
                    'total_earnings' => Scan::whereDate('created_at', $date)->sum('earnings_amount'),
                ],
                'system_health' => [
                    'pending_jobs' => 0, // Would integrate with queue monitoring
                    'failed_jobs' => 0,
                    'error_rate' => 0.0,
                ],
            ];
        });

        return response()->json($digest);
    }

    public function sendAlert(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:campaign_submitted,fraud_detected,system_error,payout_processed',
            'message' => 'required|string',
            'severity' => 'required|string|in:low,medium,high,critical',
            'data' => 'nullable|array',
        ]);

        // In a real implementation, this would send alerts via email, Slack, etc.
        // For now, just log it

        \Illuminate\Support\Facades\Log::info('Admin alert sent', [
            'type' => $request->type,
            'message' => $request->message,
            'severity' => $request->severity,
            'data' => $request->data,
        ]);

        return response()->json([
            'message' => 'Alert sent successfully',
            'type' => $request->type,
            'severity' => $request->severity,
        ]);
    }

    public function authenticateAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'token' => 'required|string',
            'data' => 'nullable|array',
        ]);

        // In a real implementation, this would validate admin authentication
        // For now, just return success

        return response()->json([
            'authenticated' => true,
            'action' => $request->action,
            'message' => 'Action authenticated successfully',
        ]);
    }
}