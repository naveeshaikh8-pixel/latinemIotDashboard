<?php

use App\Http\Controllers\Api\VehicleTrackingController;
use Illuminate\Support\Facades\Route;

// Vehicles list
Route::get('/vehicles', [VehicleTrackingController::class, 'index']);

// IoT simulation endpoint (inserts exactly 2 records per request)
Route::post('/iot/simulate', [VehicleTrackingController::class, 'simulate']);

// Dashboard telemetry endpoints
Route::prefix('dashboard')->group(function () {
    Route::get('/summary', [VehicleTrackingController::class, 'summary']);
    Route::get('/travelled-distance', [VehicleTrackingController::class, 'travelledDistance']);
    Route::get('/engine-hours', [VehicleTrackingController::class, 'engineHours']);
    Route::get('/activity-breakdown', [VehicleTrackingController::class, 'activityBreakdown']);
    Route::get('/messages-received', [VehicleTrackingController::class, 'messagesReceived']);
    Route::get('/timeline', [VehicleTrackingController::class, 'timeline']);
    Route::get('/latest-feed', [VehicleTrackingController::class, 'latestFeed']);
});

