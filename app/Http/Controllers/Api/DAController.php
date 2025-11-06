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

class DAController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string',
            'password' => 'required|string|min:8',
            'referrer_code' => 'required|string',
            'national_id' => 'required|string',
            'dob' => 'required|date',
            'gender' => 'required|string',
            'country' => 'required|string',
            'county' => 'nullable|string',
            'sub_county' => 'nullable|string',
            'ward' => 'nullable|string',
            'address' => 'nullable|string',
            'social_platforms' => 'nullable|array',
            'followers_range' => 'nullable|string',
            'preferred_channel' => 'nullable|string',
            'preferred_wallet_type' => 'nullable|string',
            'wallet_pin' => 'nullable|digits:4',
            'consent_terms' => 'required|boolean',
            'consent_data' => 'required|boolean',
            'consent_ethics' => 'required|boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'da',
        ]);

        // Validate referrer DA or DCD, or use default admin
        $referrer = null;
        $referredById = null;
        $referrerType = null;

        if ($request->referrer_code) {
            // Check if it's a DA referral code
            $referrer = DA::where('referral_code', $request->referrer_code)->first();
            if ($referrer) {
                $referredById = $referrer->id;
                $referrerType = 'da';
            } else {
                // Check if it's a DCD referral code
                $referrer = DCD::where('referral_code', $request->referrer_code)->first();
                if ($referrer) {
                    $referredById = $referrer->id;
                    $referrerType = 'dcd';
                }
            }
        }

        // If no referrer found, use default admin referral
        if (!$referrer) {
            $admin = Admin::getDefaultAdmin();
            if ($admin) {
                // Create a referral record for admin attribution
                $referredById = null; // Admin referrals are tracked differently
                Log::info('DA registered without referrer, using default admin', [
                    'new_da_email' => $request->email,
                    'admin_id' => $admin->id
                ]);
            }
        }

        $referralCode = 'DA_' . strtoupper(Str::random(6));

        $da = DA::create([
            'user_id' => $user->id,
            'referral_code' => $referralCode,
            'venture_shares' => 0,
            'national_id' => $request->national_id,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'country' => $request->country,
            'county' => $request->county,
            'sub_county' => $request->sub_county,
            'ward' => $request->ward,
            'address' => $request->address,
            'social_platforms' => $request->social_platforms,
            'followers_range' => $request->followers_range,
            'preferred_channel' => $request->preferred_channel,
            'preferred_wallet_type' => $request->preferred_wallet_type,
            'wallet_pin_hash' => $request->wallet_pin ? Hash::make($request->wallet_pin) : null,
            'consent_terms' => $request->consent_terms,
            'consent_data' => $request->consent_data,
            'consent_ethics' => $request->consent_ethics,
            'referred_by_da_id' => $referredById,
        ]);

        // Log referral linking
        if ($referrer) {
            Log::info('DA referred by existing DA', ['new_da_id' => $da->id, 'referrer_da_id' => $referrer->id]);
        }

        // Notify admin of new DA registration
        $this->notifyAdminOfNewRegistration('DA', $da->id, $user, $da);

        // TODO: Send welcome email with referral code

        return response()->json([
            'message' => 'DA created successfully',
            'user' => $user,
            'da' => $da,
        ], 201);
    }

    /**
     * Notify admin of new user registration
     */
    private function notifyAdminOfNewRegistration(string $userType, int $userId, $user, $userModel)
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