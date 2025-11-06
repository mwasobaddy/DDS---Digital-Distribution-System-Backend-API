<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|string|exists:campaigns,id',
        ]);

        $campaign = Campaign::with('client')->findOrFail($request->campaign_id);

        if ($campaign->status !== 'APPROVED') {
            return response()->json(['error' => 'Invoice can only be generated for approved campaigns'], 400);
        }

        // Generate invoice data
        $invoiceData = [
            'invoice_number' => 'INV-' . $campaign->id . '-' . now()->format('Ymd'),
            'campaign' => $campaign,
            'client' => $campaign->client,
            'amount' => $campaign->budget,
            'generated_at' => now(),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('invoices.campaign_invoice', $invoiceData);

        $filename = $invoiceData['invoice_number'] . '.pdf';
        $path = 'invoices/' . $filename;

        Storage::put($path, $pdf->output());

        // TODO: Send invoice email to client

        return response()->json([
            'message' => 'Invoice generated successfully',
            'invoice_number' => $invoiceData['invoice_number'],
            'download_url' => Storage::url($path),
            'amount' => $campaign->budget,
        ]);
    }
}