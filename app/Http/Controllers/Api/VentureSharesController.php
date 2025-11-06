<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DA;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VentureSharesController extends Controller
{
    public function allocate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'required|string|in:da_referral,da_to_dcd_referral,dcd_early_adopter,milestone_bonus',
            'amount' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->user_id);

        // For DA referrals, update venture shares
        if ($user->role === 'da') {
            $da = DA::where('user_id', $request->user_id)->first();
            if ($da) {
                $da->increment('venture_shares', $request->amount);
            }
        }

        Log::info('Venture shares allocated', [
            'user_id' => $request->user_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'reason' => $request->reason,
        ]);

        return response()->json([
            'message' => 'Venture shares allocated successfully',
            'user_id' => $request->user_id,
            'type' => $request->type,
            'amount' => $request->amount,
        ]);
    }

    public function batchAllocate(Request $request)
    {
        $request->validate([
            'allocations' => 'required|array',
            'allocations.*.user_id' => 'required|integer|exists:users,id',
            'allocations.*.type' => 'required|string|in:da_referral,da_to_dcd_referral,dcd_early_adopter,milestone_bonus',
            'allocations.*.amount' => 'required|integer|min:1',
            'allocations.*.reason' => 'nullable|string',
        ]);

        $results = [];
        foreach ($request->allocations as $allocation) {
            try {
                $user = User::findOrFail($allocation['user_id']);

                if ($user->role === 'da') {
                    $da = DA::where('user_id', $allocation['user_id'])->first();
                    if ($da) {
                        $da->increment('venture_shares', $allocation['amount']);
                    }
                }

                $results[] = [
                    'user_id' => $allocation['user_id'],
                    'status' => 'success',
                    'amount' => $allocation['amount'],
                ];

                Log::info('Batch venture shares allocated', [
                    'user_id' => $allocation['user_id'],
                    'type' => $allocation['type'],
                    'amount' => $allocation['amount'],
                    'reason' => $allocation['reason'] ?? null,
                ]);

            } catch (\Exception $e) {
                $results[] = [
                    'user_id' => $allocation['user_id'],
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'message' => 'Batch allocation completed',
            'results' => $results,
            'total_processed' => count($results),
            'successful' => collect($results)->where('status', 'success')->count(),
        ]);
    }
}