<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DAController;
use App\Http\Controllers\Api\DCDController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\AdminCampaignController;
use App\Http\Controllers\Api\PayoutController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\QRController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\VentureSharesController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\ScanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Test endpoint to verify deployment
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'DDS API is working!',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// DDS API Routes
Route::post('/da/create', [DAController::class, 'create']);
Route::post('/dcd/create', [DCDController::class, 'create']);
Route::post('/client/create', [ClientController::class, 'create']);
Route::post('/campaign/create', [CampaignController::class, 'create']);
Route::post('/campaign/{campaignId}/approve', [AdminCampaignController::class, 'approve'])->middleware('signed')->name('api.campaign.approve');
Route::post('/campaign/{campaignId}/reject', [AdminCampaignController::class, 'reject'])->middleware('signed')->name('api.campaign.reject');
Route::post('/campaign/{campaignId}/mark-paid', [AdminCampaignController::class, 'markPaid'])->middleware('signed')->name('api.campaign.mark-paid');
Route::post('/invoice/generate', [InvoiceController::class, 'generate']);
Route::post('/qr/generate-dcd', [QRController::class, 'generateDcd']);
Route::post('/qr/regenerate/{dcdId}', [QRController::class, 'regenerate']);
Route::get('/scan/validate', [ScanController::class, 'validateQr']);
Route::get('/scans/analytics/{campaignId}', [ScanController::class, 'analytics']);
Route::post('/referral/track', [ReferralController::class, 'track']);
Route::post('/earnings/calculate', [PayoutController::class, 'calculateEarnings']);
Route::post('/ventureshares/allocate', [VentureSharesController::class, 'allocate']);
Route::post('/ventureshares/batch-allocate', [VentureSharesController::class, 'batchAllocate']);
Route::get('/admin/digest', [AdminController::class, 'digest']);
Route::post('/alerts/send', [AdminController::class, 'sendAlert']);
Route::post('/admin/action/authenticate', [AdminController::class, 'authenticateAction']);
Route::get('/payouts/generate-report', [PayoutController::class, 'generateReport']);
Route::post('/scan/track', [ScanController::class, 'trackApi'])->name('api.scan.track');