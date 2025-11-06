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

        // Send approval email to client
        if ($campaign->client && $campaign->client->email) {
            Mail::to($campaign->client->email)->send(new CampaignApproved($campaign));
        }

        // TODO: Generate invoice PDF
        // TODO: Send invoice email to client

        Log::info("Campaign approved", ['campaign_id' => $campaignId]);

        return response()->json(['message' => 'Campaign approved']);
    }

    public function reject(Request $request, $campaignId)
    {
        $request->validate(['reason' => 'required|string']);

        $campaign = Campaign::findOrFail($campaignId);
        $campaign->update(['status' => 'REJECTED']);

        // Send rejection email to client
        if ($campaign->client && $campaign->client->email) {
            Mail::to($campaign->client->email)->send(new CampaignRejected($campaign, $request->reason));
        }

        Log::info("Campaign rejected", ['campaign_id' => $campaignId, 'reason' => $request->reason]);

        return response()->json(['message' => 'Campaign rejected']);
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