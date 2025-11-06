<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\Client;
use App\Models\Campaign;
use App\Mail\AdminNewUserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string',
            'company_name' => 'required|string',
            'account_type' => 'nullable|string',
            'country' => 'nullable|string',
            'referral_code' => 'nullable|string',
            'billing_info' => 'nullable|array',
            'campaign' => 'required|array',
            'campaign.name' => 'required|string',
            'campaign.type' => 'nullable|string',
            'campaign.product_link' => 'required|url',
            'campaign.explainer_video' => 'nullable|url',
            'campaign.objective' => 'nullable|string',
            'campaign.budget' => 'required|numeric|min:0',
            'campaign.safety_preferences' => 'nullable|array',
            'campaign.target_country' => 'nullable|string',
            'campaign.county' => 'nullable|string',
            'campaign.subcounty' => 'nullable|string',
            'campaign.ward' => 'nullable|string',
            'campaign.business_types' => 'nullable|array',
            'campaign.start_date' => 'nullable|date',
            'campaign.end_date' => 'nullable|date|after:campaign.start_date',
        ]);

        // Auto-generate password if not provided
        $password = $request->password ?: $this->generateSecurePassword();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($password),
            'role' => 'client',
        ]);

        $client = Client::create([
            'user_id' => $user->id,
            'company_name' => $request->company_name,
            'account_type' => $request->account_type,
            'country' => $request->country,
            'referral_code' => $request->referral_code,
            'contact_person' => $request->name,
            'billing_info' => $request->billing_info ?? [],
        ]);

        // Create the campaign for the new client
        $campaign = Campaign::create([
            'id' => strtoupper('CAMP_' . uniqid()),
            'client_id' => $client->id,
            'title' => $request->campaign['name'],
            'campaign_type' => $request->campaign['type'],
            'description' => $request->campaign['objective'] ?? null,
            'product_url' => $request->campaign['product_link'],
            'explainer_video_url' => $request->campaign['explainer_video'] ?? null,
            'objective' => $request->campaign['objective'],
            'content_safety' => $request->campaign['safety_preferences'] ? implode(',', $request->campaign['safety_preferences']) : null,
            'business_types' => $request->campaign['business_types'] ?? [],
            'budget' => $request->campaign['budget'],
            'rate_per_scan' => 10, // Default rate: 10 KES per scan
            'status' => 'SUBMITTED',
            'target_counties' => $request->campaign['county'] ? [$request->campaign['county']] : [],
            'target_regions' => [
                'country' => $request->campaign['target_country'],
                'county' => $request->campaign['county'],
                'subcounty' => $request->campaign['subcounty'],
                'ward' => $request->campaign['ward'],
            ],
            'start_date' => $request->campaign['start_date'],
            'end_date' => $request->campaign['end_date'],
        ]);

        // TODO: Send welcome email with generated password

        // Notify admin of new client registration
        $this->notifyAdminOfNewRegistration('Client', $client->id, $user, $client);

        return response()->json([
            'message' => 'Client and campaign created successfully',
            'user' => $user,
            'client' => $client,
            'campaign' => $campaign,
        ], 201)->header('Access-Control-Allow-Origin', '*')
                  ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
                  ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
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

    /**
     * Generate a secure random password
     */
    private function generateSecurePassword($length = 12)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $password;
    }
}