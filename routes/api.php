<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CourtController;
use App\Http\Controllers\Api\V1\BookingController;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Public Routes
    Route::get('/courts', [CourtController::class, 'index']);
    Route::get('/courts/{court}', [CourtController::class, 'show']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/logout', [AuthController::class, 'logout']);

        // Bookings
        Route::apiResource('/bookings', BookingController::class);
    });
});
