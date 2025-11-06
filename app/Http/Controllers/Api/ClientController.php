<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\Client;
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
            'password' => 'required|string|min:8',
            'company_name' => 'required|string',
            'account_type' => 'nullable|string',
            'country' => 'nullable|string',
            'referral_code' => 'nullable|string',
            'billing_info' => 'nullable|array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
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

        // TODO: Send welcome email

        // Notify admin of new client registration
        $this->notifyAdminOfNewRegistration('Client', $client->id, $user, $client);

        return response()->json([
            'message' => 'Client created successfully',
            'user' => $user,
            'client' => $client,
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