<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReferralController extends Controller
{
    public function track(Request $request)
    {
        $request->validate([
            'referrer_id' => 'required|integer|exists:users,id',
            'referee_id' => 'required|integer|exists:users,id',
            'type' => 'required|string|in:da_to_da,da_to_dcd,client_referral',
            'event' => 'required|string|in:registration,first_scan,first_campaign',
        ]);

        // Check if referral already exists
        $existingReferral = Referral::where('referrer_id', $request->referrer_id)
            ->where('referee_id', $request->referee_id)
            ->first();

        if ($existingReferral) {
            return response()->json([
                'message' => 'Referral already tracked',
                'referral_id' => $existingReferral->id,
            ]);
        }

        $referral = Referral::create([
            'referrer_id' => $request->referrer_id,
            'referee_id' => $request->referee_id,
            'type' => $request->type,
            'status' => 'active',
            'event' => $request->event,
        ]);

        Log::info('Referral tracked', [
            'referral_id' => $referral->id,
            'referrer_id' => $request->referrer_id,
            'referee_id' => $request->referee_id,
            'type' => $request->type,
            'event' => $request->event,
        ]);

        return response()->json([
            'message' => 'Referral tracked successfully',
            'referral_id' => $referral->id,
        ], 201);
    }
}