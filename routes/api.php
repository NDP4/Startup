<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BusController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CrewAssignmentController;
use App\Http\Controllers\Api\SeatConfigurationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Handle Unauthorized Access
Route::middleware('api')->get('/unauthorized', function () {
    return response()->json([
        'success' => false,
        'message' => 'Unauthorized',
        'error' => 'Token tidak valid atau telah kadaluarsa'
    ], 401);
})->name('login');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/user/register', [UserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login']);
Route::get('/user/detail', [UserController::class, 'detail'])->middleware('auth:sanctum');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/user/logout', [UserController::class, 'logout']);

    // User routes
    Route::get('/users', [UserController::class, 'index']); // Admin only - get all users
    Route::get('/users/{id?}', [UserController::class, 'detail']); // Get specific user or self

    // Bus routes
    Route::apiResource('buses', BusController::class);

    // Booking routes
    Route::apiResource('bookings', BookingController::class);

    // Route routes
    Route::apiResource('routes', RouteController::class);

    // Review routes
    Route::apiResource('reviews', ReviewController::class);

    // Payment routes
    Route::apiResource('payments', PaymentController::class);

    // Crew Assignment routes
    Route::apiResource('crew-assignments', CrewAssignmentController::class);

    // Seat Configuration routes
    Route::apiResource('seat-configurations', SeatConfigurationController::class);
});
