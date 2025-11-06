<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DCD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRController extends Controller
{
    public function generateDcd(Request $request)
    {
        $request->validate([
            'dcd_id' => 'required|string|exists:dcds,id',
            'campaign_id' => 'nullable|string|exists:campaigns,id',
        ]);

        $dcd = DCD::findOrFail($request->dcd_id);

        // Generate QR code data
        $campaignId = $request->campaign_id ?? 'PENDING';
        $qrData = 'DCD_' . $dcd->id . '_CAMP_' . $campaignId;

        // Generate QR code image
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->generate($qrData);

        $filename = 'qr_' . $dcd->id . '.svg';
        $path = 'qrcodes/' . $filename;

        Storage::put($path, $qrCode);

        // Update DCD with QR code path
        $dcd->update(['qr_code' => $qrData]);

        return response()->json([
            'message' => 'QR code generated successfully',
            'dcd_id' => $dcd->id,
            'qr_code_data' => $qrData,
            'download_url' => Storage::url($path),
        ]);
    }

    public function regenerate(Request $request, $dcdId)
    {
        $dcd = DCD::findOrFail($dcdId);

        // Generate new QR code data
        $qrData = 'DCD_' . $dcd->id . '_CAMP_PENDING';

        // Generate QR code image
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->generate($qrData);

        $filename = 'qr_' . $dcd->id . '.svg';
        $path = 'qrcodes/' . $filename;

        Storage::put($path, $qrCode);

        // Update DCD with new QR code
        $dcd->update(['qr_code' => $qrData]);

        return response()->json([
            'message' => 'QR code regenerated successfully',
            'dcd_id' => $dcd->id,
            'qr_code_data' => $qrData,
            'download_url' => Storage::url($path),
        ]);
    }
}