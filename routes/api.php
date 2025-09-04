<?php

use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::prefix('v1')->group(function () {

    // Provider routes (public)
    Route::get('/providers', [ProviderController::class, 'index']);
    Route::get('/providers/{provider}', [ProviderController::class, 'show']);
    Route::get('/providers/{provider}/availability', [ProviderController::class, 'availability']);

    // Search and filters
    Route::get('/search/providers', [ProviderController::class, 'search']);
    Route::get('/featured/providers', [ProviderController::class, 'featured']);

    // Booking routes (public for guest bookings)
    Route::post('/bookings/guest', [BookingController::class, 'createGuestBooking']);
    Route::get('/bookings/{booking}/status', [BookingController::class, 'status']);
});

// Protected API routes (require authentication)
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // User info
    Route::get('/user', function (Request $request) {
        return $request->user()->load('provider');
    });

    // Provider management (authenticated users only)
    Route::post('/providers', [ProviderController::class, 'store']);
    Route::put('/providers/{provider}', [ProviderController::class, 'update']);
    Route::delete('/providers/{provider}', [ProviderController::class, 'destroy']);

    // Booking management (authenticated users)
    Route::apiResource('bookings', BookingController::class)->except(['index']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm']);
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    Route::post('/bookings/{booking}/complete', [BookingController::class, 'complete']);

    // Provider-specific booking routes
    Route::get('/provider/bookings', [BookingController::class, 'providerBookings']);
    Route::get('/provider/dashboard', [ProviderController::class, 'dashboard']);
});

// Webhook routes (no authentication required, but should verify signatures)
Route::prefix('webhooks')->group(function () {
    Route::post('/stripe', [BookingController::class, 'stripeWebhook']);
    Route::post('/clerk', [BookingController::class, 'clerkWebhook']);
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});
