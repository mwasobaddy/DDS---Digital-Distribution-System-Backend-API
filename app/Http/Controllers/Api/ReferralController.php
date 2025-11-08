<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\Admin;
use App\Models\DA;
use App\Models\DCD;
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

    /**
     * Validate a referral code against admin, DA, or DCD records
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'referral_code' => 'required|string|min:3|max:20',
        ]);

        $code = strtoupper(trim($request->referral_code));

        // Check Admin table
        $admin = Admin::where('default_referral_code', $code)->first();
        if ($admin) {
            return response()->json([
                'valid' => true,
                'type' => 'admin',
                'referrer_name' => $admin->name,
                'message' => 'Valid admin referral code',
            ]);
        }

        // Check DA table
        $da = DA::where('referral_code', $code)->first();
        if ($da) {
            return response()->json([
                'valid' => true,
                'type' => 'da',
                'referrer_name' => $da->user->name ?? 'Unknown DA',
                'da_id' => $da->id,
                'message' => 'Valid Digital Ambassador referral code',
            ]);
        }

        // Check DCD table
        $dcd = DCD::where('referral_code', $code)->first();
        if ($dcd) {
            return response()->json([
                'valid' => true,
                'type' => 'dcd',
                'referrer_name' => $dcd->business_name ?? 'Unknown DCD',
                'dcd_id' => $dcd->id,
                'message' => 'Valid Digital Content Distributor referral code',
            ]);
        }

        // Code not found
        return response()->json([
            'valid' => false,
            'message' => 'Invalid referral code. Please check with your referring ambassador or contact support.',
        ], 404);
    }
}