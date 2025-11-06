<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'campaign_type' => 'nullable|string',
            'product_url' => 'required|url',
            'explainer_video_url' => 'nullable|url',
            'objective' => 'nullable|string',
            'content_safety' => 'nullable|string',
            'business_types' => 'nullable|array',
            'budget' => 'required|numeric|min:0',
            'rate_per_scan' => 'required|numeric|min:0',
            'target_counties' => 'required|array',
            'target_regions' => 'nullable|array',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        // Assume authenticated client
        $clientId = 1; // TODO: from auth

        $campaign = Campaign::create([
            'id' => strtoupper('CAMP_' . uniqid()),
            'client_id' => $clientId,
            'title' => $request->title,
            'campaign_type' => $request->campaign_type,
            'description' => $request->description,
            'product_url' => $request->product_url,
            'explainer_video_url' => $request->explainer_video_url,
            'objective' => $request->objective,
            'content_safety' => $request->content_safety,
            'business_types' => $request->business_types,
            'budget' => $request->budget,
            'rate_per_scan' => $request->rate_per_scan,
            'status' => 'SUBMITTED',
            'target_counties' => $request->target_counties,
            'target_regions' => $request->target_regions,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        // TODO: Send confirmation email to client
        // TODO: Send alert email to admin with approve/reject links

        return response()->json([
            'message' => 'Campaign created successfully',
            'campaign' => $campaign,
        ], 201);
    }
}