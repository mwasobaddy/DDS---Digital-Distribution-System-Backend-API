<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;

Route::get('/', function () {
    return view('welcome');
});

// DDS Scan Route
Route::get('/s/{qrCode}', [ScanController::class, 'track'])->name('scan.track');

// Error routes
Route::get('/error/invalid-qr', function () {
    return response('Invalid QR Code', 400);
})->name('error.invalid-qr');

Route::get('/error/campaign-ended', function () {
    return response('Campaign Ended', 404);
})->name('error.campaign-ended');

Route::get('/error/system', function () {
    return response('System Error', 500);
})->name('error.system');
