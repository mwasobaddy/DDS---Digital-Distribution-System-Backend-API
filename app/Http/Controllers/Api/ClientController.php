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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function create(Request $request)
    {
        try {
            // Skip traditional validation and validate manually
            $errors = [];
            
            // Manual validation to avoid password field issues
            if (empty($request->name)) $errors['name'] = ['Name is required'];
            if (empty($request->email)) $errors['email'] = ['Email is required'];
            if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) $errors['email'] = ['Email must be valid'];
            if (empty($request->phone)) $errors['phone'] = ['Phone is required'];
            if (empty($request->company_name)) $errors['company_name'] = ['Company name is required'];
            if (empty($request->campaign)) $errors['campaign'] = ['Campaign data is required'];
            
            // Check if email already exists
            if (User::where('email', $request->email)->exists()) {
                $errors['email'] = ['Email already exists'];
            }
            
            // Campaign validation
            if ($request->campaign) {
                if (empty($request->campaign['name'])) $errors['campaign.name'] = ['Campaign name is required'];
                if (empty($request->campaign['product_link'])) $errors['campaign.product_link'] = ['Product link is required'];
                if (!empty($request->campaign['product_link']) && !filter_var($request->campaign['product_link'], FILTER_VALIDATE_URL)) {
                    $errors['campaign.product_link'] = ['Product link must be a valid URL'];
                }
                if (empty($request->campaign['budget']) || !is_numeric($request->campaign['budget']) || $request->campaign['budget'] < 0) {
                    $errors['campaign.budget'] = ['Budget must be a valid positive number'];
                }
            }
            
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ], 422);
            }

            // Generate secure password
            $plainPassword = $this->generateSecurePassword();
            if (empty($plainPassword)) {
                Log::error('Password generation failed');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate secure password'
                ], 500);
            }

            // Use database transaction for data integrity
            return DB::transaction(function () use ($request, $plainPassword) {
                
                // Create user with explicitly hashed password
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->password = Hash::make($plainPassword);
                $user->role = 'client';
                $user->save();

                if (!$user->id) {
                    throw new \Exception('Failed to create user');
                }

                Log::info('User created successfully', ['user_id' => $user->id, 'email' => $user->email]);

                // Create client profile
                $client = new Client();
                $client->user_id = $user->id;
                $client->company_name = $request->company_name;
                $client->account_type = $request->account_type;
                $client->country = $request->country;
                $client->referral_code = $request->referral_code;
                $client->contact_person = $request->name;
                $client->billing_info = $request->billing_info ?? [];
                $client->save();

                if (!$client->id) {
                    throw new \Exception('Failed to create client profile');
                }

                Log::info('Client created successfully', ['client_id' => $client->id, 'user_id' => $user->id]);

                // Prevent multiple overlapping campaigns: user may only run one campaign at a time
                $now = Carbon::now();
                $hasActive = Campaign::where('client_id', $user->id)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', $now);
                    })->exists();

                if ($hasActive) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You already have an active campaign. You can only create a new campaign after the current campaign ends.'
                    ], 422);
                }

                // Create the campaign for the new client
                $campaign = new Campaign();
                $campaign->id = strtoupper('CAMP_' . uniqid());
                // store the user id as client identifier (campaigns.client_id references users.id)
                $campaign->client_id = $user->id;
                $campaign->title = $request->campaign['name'];
                $campaign->campaign_type = $request->campaign['type'];
                $campaign->description = $request->campaign['objective'] ?? null;
                $campaign->product_url = $request->campaign['product_link'];
                $campaign->explainer_video_url = $request->campaign['explainer_video'] ?? null;
                $campaign->objective = $request->campaign['objective'];
                $campaign->content_safety = $request->campaign['safety_preferences'] ? implode(',', $request->campaign['safety_preferences']) : null;
                $campaign->business_types = $request->campaign['business_types'] ?? [];
                $campaign->budget = $request->campaign['budget'];
                $campaign->rate_per_scan = 10; // Default rate: 10 KES per scan
                $campaign->status = 'SUBMITTED';
                $campaign->target_counties = $request->campaign['county'] ? [$request->campaign['county']] : [];
                $campaign->target_regions = [
                    'country' => $request->campaign['target_country'],
                    'county' => $request->campaign['county'],
                    'subcounty' => $request->campaign['subcounty'],
                    'ward' => $request->campaign['ward'],
                ];
                $campaign->start_date = $request->campaign['start_date'];
                $campaign->end_date = $request->campaign['end_date'];
                $campaign->save();

                if (!$campaign->id) {
                    throw new \Exception('Failed to create campaign');
                }

                Log::info('Campaign created successfully', ['campaign_id' => $campaign->id, 'client_id' => $client->id]);

                // Send welcome email with generated password
                $this->sendWelcomeEmail($user, $plainPassword);

                // Notify admin of new client registration
                $this->notifyAdminOfNewRegistration('Client', $client->id, $user, $client);

                return response()->json([
                    'success' => true,
                    'message' => 'Client and campaign created successfully',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'phone' => $user->phone,
                            'role' => $user->role
                        ],
                        'client' => [
                            'id' => $client->id,
                            'company_name' => $client->company_name,
                            'account_type' => $client->account_type,
                            'country' => $client->country
                        ],
                        'campaign' => [
                            'id' => $campaign->id,
                            'title' => $campaign->title,
                            'budget' => $campaign->budget,
                            'status' => $campaign->status
                        ]
                    ],
                    'generated_password' => $plainPassword
                ], 201)->header('Access-Control-Allow-Origin', '*')
                          ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
                          ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            });

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error during client creation', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error' => 'A database error occurred while creating your account. Please try again.'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Error during client creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating your account',
                'error' => $e->getMessage()
            ], 500);
        }
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
        try {
            // Use more secure random generation
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
            $password = '';
            $charactersLength = strlen($characters);
            
            for ($i = 0; $i < $length; $i++) {
                $password .= $characters[random_int(0, $charactersLength - 1)];
            }
            
            // Ensure password has at least one uppercase, lowercase, number, and special character
            if (!preg_match('/[A-Z]/', $password) || 
                !preg_match('/[a-z]/', $password) || 
                !preg_match('/[0-9]/', $password) || 
                !preg_match('/[^A-Za-z0-9]/', $password)) {
                // Regenerate if doesn't meet criteria
                return $this->generateSecurePassword($length);
            }
            
            return $password;
            
        } catch (\Exception $e) {
            Log::error('Password generation error', ['error' => $e->getMessage()]);
            // Fallback to basic generation
            return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*'), 0, $length);
        }
    }

    /**
     * Send welcome email to new client with login credentials
     */
    private function sendWelcomeEmail($user, $plainPassword)
    {
        try {
            // TODO: Create and send welcome email with login credentials
            // You can create a Mailable class for this
            Mail::raw("Welcome to Daya!\n\nYour account has been created successfully.\n\nEmail: {$user->email}\nPassword: {$plainPassword}\n\nPlease change your password after first login.\n\nBest regards,\nDaya Team", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Welcome to Daya - Your Account Details');
            });

            Log::info("Welcome email sent to client", [
                'client_id' => $user->id,
                'client_email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send welcome email to client", [
                'client_id' => $user->id,
                'client_email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}