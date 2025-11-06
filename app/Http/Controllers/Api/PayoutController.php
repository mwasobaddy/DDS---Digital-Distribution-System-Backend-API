<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PayoutController extends Controller
{
    public function generateReport(Request $request)
    {
        $period = now()->subMonth()->format('Y-m');

        $earnings = Earning::where('period', $period)
            ->where('status', 'pending')
            ->with('user')
            ->get()
            ->groupBy('user_id')
            ->map(function ($userEarnings) {
                $user = $userEarnings->first()->user;
                $total = $userEarnings->sum('amount');
                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'total_amount' => $total,
                    'earnings' => $userEarnings->toArray(),
                ];
            });

        // Generate CSV
        $csv = "User ID,Name,Email,Total Amount\n";
        foreach ($earnings as $earning) {
            $csv .= "{$earning['user_id']},{$earning['name']},{$earning['email']},{$earning['total_amount']}\n";
        }

        $filename = "payout_report_{$period}.csv";
        Storage::put($filename, $csv);

        // TODO: Send email to admin with download link

        return response()->json([
            'message' => 'Payout report generated',
            'filename' => $filename,
            'total_users' => $earnings->count(),
            'total_amount' => $earnings->sum('total_amount'),
        ]);
    }

    public function calculateEarnings(Request $request)
    {
        $request->validate([
            'period' => 'nullable|date_format:Y-m',
        ]);

        $period = $request->period ?? now()->subMonth()->format('Y-m');

        // Dispatch job to calculate earnings for the period
        // This would typically process all pending scans and create earnings records
        // For now, we'll simulate the calculation

        $processedScans = Scan::where('created_at', 'like', $period . '%')
            ->count();

        // In a real implementation, this would dispatch a job to process earnings
        // ProcessEarnings::dispatch($period);

        return response()->json([
            'message' => 'Earnings calculation initiated',
            'period' => $period,
            'scans_to_process' => $processedScans,
            'status' => 'processing',
        ]);
    }
}