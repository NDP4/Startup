<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentNotificationController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusProductController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\CustomerReviewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Customer\CrewAssignmentController;
use App\Http\Controllers\Customer\CrewAssignmentController as CustomerCrewAssignmentController;
use App\Http\Controllers\ChatController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/payment/checkout/{booking}', [PaymentController::class, 'checkout'])
        ->name('payment.checkout');

    Route::get('/payment/success', [PaymentController::class, 'success'])
        ->name('payment.success');
    Route::get('/payment/pending', [PaymentController::class, 'pending'])
        ->name('payment.pending');
    Route::get('/payment/error', [PaymentController::class, 'error'])
        ->name('payment.error');
    Route::get('/payment/cancelled', [PaymentController::class, 'cancelled'])
        ->name('payment.cancelled');

    // Receipt routes
    Route::get('/booking/{booking}/receipt', [ReceiptController::class, 'show'])
        ->name('booking.receipt');
    Route::get('/booking/{booking}/receipt/download', [ReceiptController::class, 'download'])
        ->name('booking.receipt.download');

    // Review routes
    Route::get('/reviews/{booking}/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews/{booking}', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
});

// Bus Product Routes
Route::get('/buses', [BusProductController::class, 'index'])->name('buses.index');
Route::get('/buses/{bus}', [BusProductController::class, 'show'])->name('buses.show');

// Booking Routes - Protected by auth middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/booking/{bus}', [BookingController::class, 'create'])
        ->middleware('validate.booking')
        ->name('booking.create');
    Route::post('/booking/{bus}', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/payment/checkout/{booking}', [PaymentController::class, 'checkout'])->name('payment.checkout');
});

// Customer Routes (Auth Required)uth middleware
Route::middleware(['auth', 'verified'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/booking/{bus}', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking/{bus}', [BookingController::class, 'store'])->name('booking.store');
});

// Customer Routes (Auth Required)
Route::middleware(['auth', 'verified'])->prefix('customer')->name('customer.')->group(function () {
    // Customer Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])
        ->name('customer.dashboard');

    // Profile
    Route::get('/profile', [CustomerProfileController::class, 'edit'])
        ->name('customer.profile.edit');
    Route::patch('/profile', [CustomerProfileController::class, 'update'])
        ->name('customer.profile.update');

    // Booking Process
    Route::get('/buses/{bus}/book', [BookingController::class, 'create'])
        ->name('booking.create');
    Route::post('/buses/{bus}/book', [BookingController::class, 'store'])
        ->name('booking.store');

    // Customer Bookings
    Route::get('/my-bookings', [CustomerBookingController::class, 'index'])
        ->name('customer.bookings.index');
    Route::get('/my-bookings/{booking}', [CustomerBookingController::class, 'show'])
        ->name('customer.bookings.show');

    // Customer Reviews
    Route::get('/my-reviews', [CustomerReviewController::class, 'index'])
        ->name('customer.reviews.index');

    // Crew Assignment Routes
    Route::get('/crew-assignments', [CrewAssignmentController::class, 'index'])->name('crew-assignments.index');
    Route::get('/crew-assignments/{assignment}', [CrewAssignmentController::class, 'show'])->name('crew-assignments.show');
});

Route::post('payment/notification', [PaymentNotificationController::class, 'handle'])
    ->name('payment.notification');

Route::middleware(['auth', 'verified'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('profile/edit', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [CustomerProfileController::class, 'update'])->name('profile.update');

    // Booking routes
    Route::get('bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [CustomerBookingController::class, 'show'])->name('bookings.show');

    // Review routes
    Route::get('reviews', [CustomerReviewController::class, 'index'])->name('reviews.index');
    Route::get('reviews/{review}', [CustomerReviewController::class, 'show'])->name('reviews.show');

    // Crew Assignment routes
    Route::get('crew-assignments', [CustomerCrewAssignmentController::class, 'index'])->name('crew-assignments.index');
    Route::get('crew-assignments/{assignment}', [CustomerCrewAssignmentController::class, 'show'])->name('crew-assignments.show');
});

Route::post('payment/notification', [PaymentNotificationController::class, 'handle'])
    ->name('payment.notification');

Route::post('/payment/{booking}/update-status', [PaymentController::class, 'updateStatus'])
    ->name('payment.update-status');

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Remove or comment out the duplicate route group
// Route::middleware(['auth'])->group(function () {
//     Route::get('/booking/{bus}', [BookingController::class, 'create'])->name('booking.create');
//     Route::post('/booking/{bus}', [BookingController::class, 'store'])->name('booking.store');
// });

// Replace with this single route group
Route::middleware(['auth', \App\Http\Middleware\ValidateBookingAvailability::class])->group(function () {
    Route::get('/booking/{bus}', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking/{bus}', [BookingController::class, 'store'])->name('booking.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Existing routes...

    // Chat Routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
});
