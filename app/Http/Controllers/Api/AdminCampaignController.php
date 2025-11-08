<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CampaignApproved;
use App\Mail\CampaignRejected;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminCampaignController extends Controller
{
    public function approve(Request $request, $campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $campaign->update(['status' => 'APPROVED']);

        // Get the user (client) associated with this campaign
        $user = \App\Models\User::find($campaign->client_id);
        
        // Send approval email to client
        if ($user && $user->email) {
            try {
                Mail::to($user->email)->send(new CampaignApproved($campaign));
                Log::info("Campaign approval email sent", ['campaign_id' => $campaignId, 'user_email' => $user->email]);
            } catch (\Exception $e) {
                Log::error("Failed to send campaign approval email", [
                    'campaign_id' => $campaignId, 
                    'user_email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // TODO: Generate invoice PDF
        // TODO: Send invoice email to client

        Log::info("Campaign approved", ['campaign_id' => $campaignId]);

        return response()->json(['message' => 'Campaign approved successfully']);
    }

    public function reject(Request $request, $campaignId)
    {
        $request->validate(['reason' => 'required|string']);

        $campaign = Campaign::findOrFail($campaignId);
        $campaign->update(['status' => 'REJECTED']);

        // Get the user (client) associated with this campaign
        $user = \App\Models\User::find($campaign->client_id);
        
        // Send rejection email to client
        if ($user && $user->email) {
            try {
                Mail::to($user->email)->send(new CampaignRejected($campaign, $request->reason));
                Log::info("Campaign rejection email sent", ['campaign_id' => $campaignId, 'user_email' => $user->email]);
            } catch (\Exception $e) {
                Log::error("Failed to send campaign rejection email", [
                    'campaign_id' => $campaignId, 
                    'user_email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("Campaign rejected", ['campaign_id' => $campaignId, 'reason' => $request->reason]);

        return response()->json(['message' => 'Campaign rejected successfully']);
    }

    public function markPaid(Request $request, $campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $campaign->update(['status' => 'LIVE']);

        // TODO: Update DCD QR codes to include campaign
        // TODO: Send Go-Live email to client

        Log::info("Campaign marked as paid", ['campaign_id' => $campaignId]);

        return response()->json(['message' => 'Campaign marked as paid and live']);
    }

    /**
     * Approve campaign via email link (GET request from email)
     */
    public function approveViaEmail($campaignId)
    {
        try {
            $campaign = Campaign::findOrFail($campaignId);
            
            // Check if already processed
            if ($campaign->status === 'APPROVED') {
                return $this->showActionResult('Campaign Already Approved', 
                    "Campaign '{$campaign->title}' has already been approved.", 'success', $campaign);
            }
            
            if ($campaign->status !== 'SUBMITTED') {
                return $this->showActionResult('Cannot Approve Campaign', 
                    "Campaign '{$campaign->title}' cannot be approved as it is not in submitted status.", 'error', $campaign);
            }

            // Approve the campaign
            $campaign->update(['status' => 'APPROVED']);

            // Get the user (client) associated with this campaign
            $user = \App\Models\User::find($campaign->client_id);
            
            // Send approval email to client
            if ($user && $user->email) {
                try {
                    Mail::to($user->email)->send(new CampaignApproved($campaign));
                    Log::info("Campaign approval email sent", ['campaign_id' => $campaignId, 'user_email' => $user->email]);
                } catch (\Exception $e) {
                    Log::error("Failed to send campaign approval email", [
                        'campaign_id' => $campaignId, 
                        'user_email' => $user->email,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info("Campaign approved via email", ['campaign_id' => $campaignId]);

            return $this->showActionResult('Campaign Approved Successfully', 
                "Campaign '{$campaign->title}' has been approved successfully. The client will receive a notification email.", 'success', $campaign);

        } catch (\Exception $e) {
            Log::error("Error approving campaign via email", ['campaign_id' => $campaignId, 'error' => $e->getMessage()]);
            return $this->showActionResult('Error Approving Campaign', 
                "An error occurred while approving the campaign: " . $e->getMessage(), 'error');
        }
    }

    /**
     * Reject campaign via email link (GET request from email)
     */
    public function rejectViaEmail(Request $request, $campaignId)
    {
        try {
            $campaign = Campaign::findOrFail($campaignId);
            
            // Check if already processed
            if ($campaign->status === 'REJECTED') {
                return $this->showActionResult('Campaign Already Rejected', 
                    "Campaign '{$campaign->title}' has already been rejected.", 'warning', $campaign);
            }
            
            if ($campaign->status !== 'SUBMITTED') {
                return $this->showActionResult('Cannot Reject Campaign', 
                    "Campaign '{$campaign->title}' cannot be rejected as it is not in submitted status.", 'error', $campaign);
            }

            // Get rejection reason from query parameter or use default
            $reason = $request->get('reason', 'Campaign requires review and modifications before approval.');

            // Reject the campaign
            $campaign->update(['status' => 'REJECTED']);

            // Get the user (client) associated with this campaign
            $user = \App\Models\User::find($campaign->client_id);
            
            // Send rejection email to client
            if ($user && $user->email) {
                try {
                    Mail::to($user->email)->send(new CampaignRejected($campaign, $reason));
                    Log::info("Campaign rejection email sent", ['campaign_id' => $campaignId, 'user_email' => $user->email]);
                } catch (\Exception $e) {
                    Log::error("Failed to send campaign rejection email", [
                        'campaign_id' => $campaignId, 
                        'user_email' => $user->email,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info("Campaign rejected via email", ['campaign_id' => $campaignId, 'reason' => $reason]);

            return $this->showActionResult('Campaign Rejected', 
                "Campaign '{$campaign->title}' has been rejected. The client will receive a notification email with feedback.", 'warning', $campaign);

        } catch (\Exception $e) {
            Log::error("Error rejecting campaign via email", ['campaign_id' => $campaignId, 'error' => $e->getMessage()]);
            return $this->showActionResult('Error Rejecting Campaign', 
                "An error occurred while rejecting the campaign: " . $e->getMessage(), 'error');
        }
    }

    /**
     * Show action result page
     */
    private function showActionResult($title, $message, $type, $campaign = null)
    {
        $statusColor = [
            'success' => '#28a745',
            'warning' => '#ffc107',
            'error' => '#dc3545'
        ][$type] ?? '#007bff';

        $icon = [
            'success' => '✅',
            'warning' => '⚠️',
            'error' => '❌'
        ][$type] ?? 'ℹ️';

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>$title</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 40px; background-color: #f8f9fa; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background-color: $statusColor; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 30px; }
                .icon { font-size: 48px; margin-bottom: 15px; }
                h1 { margin: 0; font-size: 24px; }
                .message { font-size: 16px; line-height: 1.6; margin-bottom: 20px; }
                .campaign-details { background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0; }
                .back-link { text-align: center; margin-top: 30px; }
                .back-link a { color: $statusColor; text-decoration: none; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='icon'>$icon</div>
                    <h1>$title</h1>
                </div>
                <div class='content'>
                    <div class='message'>$message</div>";

        if ($campaign) {
            $html .= "
                    <div class='campaign-details'>
                        <h3>Campaign Details:</h3>
                        <p><strong>ID:</strong> {$campaign->id}</p>
                        <p><strong>Title:</strong> {$campaign->title}</p>
                        <p><strong>Status:</strong> {$campaign->status}</p>
                        <p><strong>Budget:</strong> KES " . number_format($campaign->budget) . "</p>
                        <p><strong>Product URL:</strong> <a href='{$campaign->product_url}' target='_blank'>{$campaign->product_url}</a></p>
                    </div>";
        }

        $html .= "
                    <div class='back-link'>
                        <p>This action has been completed. You can close this window.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>";

        return response($html);
    }
}