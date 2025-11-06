<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\DA;
use App\Models\DCD;
use App\Mail\AdminNewUserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DCDController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string',
            'password' => 'required|string|min:8',
            'da_referral_code' => 'nullable|string',
            'national_id' => 'required|string',
            'dob' => 'required|date',
            'gender' => 'nullable|string',
            'country' => 'required|string',
            'county' => 'nullable|string',
            'sub_county' => 'nullable|string',
            'ward' => 'nullable|string',
            'business_address' => 'nullable|string',
            'gps_location' => 'nullable|array',
            'business_name' => 'nullable|string',
            'business_types' => 'nullable|array',
            'daily_foot_traffic' => 'nullable|string',
            'operating_hours' => 'nullable|string',
            'preferred_campaign_types' => 'nullable|array',
            'music_genres' => 'nullable|array',
            'content_safe' => 'nullable|boolean',
            'preferred_wallet_type' => 'nullable|string',
            'wallet_pin' => 'nullable|digits:4',
            'consent_terms' => 'required|boolean',
            'consent_data' => 'required|boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'dcd',
        ]);

        $dcdId = 'DCD_' . strtoupper(Str::random(6));
        $qrCode = $dcdId . '_CAMP_PENDING';
        $referralCode = 'DCD_' . strtoupper(Str::random(6));

        $referringDaId = null;
        if ($request->da_referral_code) {
            $da = DA::where('referral_code', $request->da_referral_code)->first();
            if ($da) {
                $referringDaId = $da->id;
                // TODO: Award venture shares to DA
            }
        }

        $dcd = DCD::create([
            'id' => $dcdId,
            'user_id' => $user->id,
            'referral_code' => $referralCode,
            'qr_code' => $qrCode,
            'referring_da_id' => $referringDaId,
            'status' => 'active',
            'national_id' => $request->national_id,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'country' => $request->country,
            'county' => $request->county,
            'sub_county' => $request->sub_county,
            'ward' => $request->ward,
            'business_address' => $request->business_address,
            'gps_location' => $request->gps_location,
            'business_name' => $request->business_name,
            'business_types' => $request->business_types,
            'daily_foot_traffic' => $request->daily_foot_traffic,
            'operating_hours' => $request->operating_hours,
            'preferred_campaign_types' => $request->preferred_campaign_types,
            'music_genres' => $request->music_genres,
            'content_safe' => $request->content_safe ?? false,
            'preferred_wallet_type' => $request->preferred_wallet_type,
            'wallet_pin_hash' => $request->wallet_pin ? Hash::make($request->wallet_pin) : null,
            'consent_terms' => $request->consent_terms,
            'consent_data' => $request->consent_data,
        ]);

        // TODO: Send welcome email with QR code PDF

        // Notify admin of new DCD registration
        $this->notifyAdminOfNewRegistration('DCD', $dcd->id, $user, $dcd);

        return response()->json([
            'message' => 'DCD created successfully',
            'user' => $user,
            'dcd' => $dcd,
        ], 201);
    }

    /**
     * Notify admin of new user registration
     */
    private function notifyAdminOfNewRegistration(string $userType, string $userId, $user, $userModel)
    {
        $admin = Admin::getDefaultAdmin();
        if (!$admin) {
            Log::warning("No active admin found for {$userType} registration notification");
            return;
        }

        try {
            // Send email notification to admin
            Mail::to($admin->email)->send(new AdminNewUserNotification($userType, $user, $userModel, $admin));

            Log::info("{$userType} registration notification email sent to admin", [
                'user_type' => $userType,
                'user_id' => $userId,
                'user_email' => $user->email,
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send {$userType} registration notification email", [
                'user_type' => $userType,
                'user_id' => $userId,
                'admin_id' => $admin->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}