<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\DA;
use App\Models\DCD;
use App\Mail\AdminNewUserNotification;
use App\Mail\DAWelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DAController extends Controller
{
    public function create(Request $request)
    {
        // Custom validation for DA registration
        $validator = Validator::make($request->all(), [
            'referral_code' => 'required|string',
            'full_name' => 'required|string|min:2|max:255',
            'national_id' => 'required|string|unique:das',
            'dob' => 'required|date|before:today|after:' . now()->subYears(100)->format('Y-m-d'),
            'gender' => 'required|in:male,female',
            'email' => 'required|email|unique:users',
            'country' => 'required|string',
            'county' => 'required|string',
            'subcounty' => 'required|string',
            'ward' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string|regex:/^[\+]?[1-9][\d\s\-\(\)]+$/',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'string|in:instagram,twitter,facebook,whatsapp,linkedin,tiktok',
            'followers' => 'required|string|in:500-1000,1000-5000,5000-50000,50000-100000,100000-500000,500000-1000000',
            'communication_channel' => 'required|string|in:whatsapp,email,in-app',
            'wallet_type' => 'required|string|in:personal,business',
            'wallet_pin' => 'required|digits:4',
            'confirm_pin' => 'required|digits:4|same:wallet_pin',
            'terms' => 'required|accepted',
            // 'cf-turnstile-response' => 'required|string', // Commented out for development
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verify Turnstile token (you may need to implement this)
            // For now, we'll skip this in development

            // Generate a random password for the user
            $generatedPassword = Str::random(12);

            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($generatedPassword),
                'role' => 'da',
            ]);

            // Validate referrer DA or DCD, or use default admin
            $referrer = null;
            $referredById = null;
            $referrerType = null;

            if ($request->referral_code) {
                // Check if it's a DA referral code
                $referrer = DA::where('referral_code', $request->referral_code)->first();
                if ($referrer) {
                    $referredById = $referrer->id;
                    $referrerType = 'da';
                } else {
                    // Check if it's a DCD referral code
                    $referrer = DCD::where('referral_code', $request->referral_code)->first();
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
                'sub_county' => $request->subcounty,
                'ward' => $request->ward,
                'address' => $request->address,
                'social_platforms' => $request->platforms,
                'followers_range' => $request->followers,
                'preferred_channel' => $request->communication_channel,
                'preferred_wallet_type' => $request->wallet_type,
                'wallet_pin_hash' => Hash::make($request->wallet_pin),
                'consent_terms' => $request->terms,
                'consent_data' => true, // Assuming data consent is included in terms
                'consent_ethics' => true, // Assuming ethics consent is included in terms
                'referred_by_da_id' => $referredById,
            ]);

            // Log referral linking
            if ($referrer) {
                Log::info('DA referred by existing DA', ['new_da_id' => $da->id, 'referrer_da_id' => $referrer->id]);
            }

            // Notify admin of new DA registration
            $this->notifyAdminOfNewRegistration('DA', $da->id, $user, $da);

            // Send welcome email to the new DA
            $this->sendWelcomeEmailToDA($user, $da, $generatedPassword, $referralCode);

            return response()->json([
                'message' => 'Digital Ambassador account created successfully! Check your email for login details.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'da' => [
                    'id' => $da->id,
                    'referral_code' => $da->referral_code,
                    'venture_shares' => $da->venture_shares,
                ],
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            // Database-specific errors
            Log::error('Database error during DA registration', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'code' => $e->getCode()
            ]);

            // Check for specific database errors
            if ($e->getCode() == 23000) { // Integrity constraint violation
                return response()->json([
                    'message' => 'Registration failed due to data conflict. This email or national ID may already be registered.',
                    'error' => 'DUPLICATE_DATA'
                ], 409);
            }

            return response()->json([
                'message' => 'Database error occurred during registration. Please try again.',
                'error' => 'DATABASE_ERROR'
            ], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // General error handling
            Log::error('Unexpected error during DA registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred during registration. Please contact support.',
                'error' => 'INTERNAL_ERROR'
            ], 500);
        }
    }

    /**
     * Send welcome email to the new DA
     */
    private function sendWelcomeEmailToDA($user, $da, $password, $referralCode)
    {
        try {
            Mail::to($user->email)->send(new DAWelcomeNotification($user, $da, $password, $referralCode));

            Log::info('DA welcome email sent successfully', [
                'da_id' => $da->id,
                'user_email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send DA welcome email', [
                'da_id' => $da->id,
                'user_email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}