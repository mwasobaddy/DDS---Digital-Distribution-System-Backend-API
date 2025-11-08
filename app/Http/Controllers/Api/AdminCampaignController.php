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
}