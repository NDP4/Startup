<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class PaymentNotificationController extends Controller
{
    public function handle(Request $request)
    {
        $notification = $request->all();

        // Extract order ID
        $orderId = $notification['order_id'];
        $bookingId = str_replace('BOOKING-', '', explode('-', $orderId)[1]);

        // Find booking
        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        // Format payment details
        $vaNumbers = $notification['va_numbers'] ?? [];
        $vaNumber = !empty($vaNumbers) ? $vaNumbers[0]['va_number'] : null;
        $bankName = !empty($vaNumbers) ? strtoupper($vaNumbers[0]['bank']) : null;

        $paymentDetails = [
            'transaction_id' => $notification['transaction_id'],
            'order_id' => $orderId,
            'payment_channel' => 'Bank Transfer', // atau sesuai channel
            'source' => 'SNAP',
            'created_at' => $notification['transaction_time'],
            'expiry_time' => $notification['expiry_time'] ?? null,
            'va_number' => $vaNumber,
            'bank' => $bankName,
            'acquiring_bank' => $bankName,
            'payment_method' => $notification['payment_type'],
            'transaction_status' => $notification['transaction_status'],
            'settlement_time' => $notification['settlement_time'] ?? null,
            'payment_type' => $notification['payment_type'],
            'gross_amount' => $notification['gross_amount'],
            'currency' => $notification['currency'] ?? 'IDR',
            'fraud_status' => $notification['fraud_status'] ?? null,
        ];

        // Create payment record
        $booking->payments()->create([
            'payment_id' => $notification['transaction_id'],
            'amount' => $notification['gross_amount'],
            'payment_type' => $bankName ? "Virtual Account {$bankName}" : $notification['payment_type'],
            'status' => $notification['transaction_status'],
            'payment_details' => $paymentDetails,
            'paid_at' => $notification['settlement_time'] ? date('Y-m-d H:i:s', strtotime($notification['settlement_time'])) : null
        ]);

        // Handle notification
        $booking->handlePaymentNotification($notification);

        return response()->json(['message' => 'Notification handled']);
    }
}
