<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentNotificationController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

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

Route::post('payment/notification', [PaymentNotificationController::class, 'handle'])
    ->name('payment.notification');
