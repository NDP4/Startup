<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function checkout(Booking $booking)
    {
        // Authorization check
        if (Auth::id() !== $booking->customer_id) {
            abort(403);
        }

        // Load relationships
        $booking->load(['customer', 'bus']);

        try {
            // Create new payment token if doesn't exist
            if (empty($booking->snap_token)) {
                $booking->createMidtransPayment();
                $booking->refresh();
            }

            if (empty($booking->snap_token)) {
                throw new \Exception('Failed to generate payment token');
            }

            return view('payment.checkout', compact('booking'));
        } catch (\Exception $e) {
            return redirect()->route('customer.bookings.index')
                ->with('error', 'Failed to process payment: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $orderId = $request->get('order_id');
        if (!$orderId) {
            return redirect()->route('filament.panel.resources.bookings.index')
                ->with('error', 'Invalid order ID');
        }

        $bookingId = str_replace('BOOKING-', '', explode('-', $orderId)[1]);
        $booking = Booking::find($bookingId);

        if ($booking && $request->get('status') === 'settlement') {
            // Generate a unique payment ID if one is not provided
            $paymentId = $request->get('transaction_id', 'PAY-' . uniqid());

            // Set payment details
            $paymentDetails = [
                'transaction_id' => $paymentId,
                'order_id' => $orderId,
                'status' => $request->get('status'),
                'payment_type' => $request->get('payment_type', 'Bank Transfer'),
                'payment_method' => $request->get('payment_type'),
                'va_numbers' => $request->get('va_numbers', []),
                'bank' => $request->get('bank'),
                'va_number' => $request->get('va_number'),
                'acquirer' => $request->get('acquirer'),
                'settlement_time' => $request->get('settlement_time', now()),
                'transaction_time' => $request->get('transaction_time', now()),
                'currency' => $request->get('currency', 'IDR'),
                'fraud_status' => $request->get('fraud_status')
            ];

            // Create payment record
            $payment = $booking->payments()->create([
                'payment_id' => $paymentId,
                'amount' => $booking->total_amount,
                'payment_type' => $request->get('payment_type', 'Bank Transfer'),
                'status' => 'success',
                'payment_details' => $paymentDetails,
                'paid_at' => $request->get('settlement_time')
                    ? Carbon::parse($request->get('settlement_time'))
                    : now()
            ]);

            // Update booking status
            $booking->update([
                'payment_status' => 'paid',
                'status' => 'confirmed'
            ]);

            return redirect()->route('booking.receipt', $booking)
                ->with('success', 'Pembayaran berhasil dikonfirmasi');
        }

        return redirect()->route('filament.panel.resources.bookings.index')
            ->with('error', 'Pembayaran gagal diverifikasi');
    }

    public function pending(Request $request)
    {
        return redirect()->route('filament.panel.resources.bookings.index')
            ->with('info', 'Menunggu pembayaran');
    }

    public function error()
    {
        return redirect()->route('filament.panel.resources.bookings.index')
            ->with('error', 'Pembayaran gagal');
    }

    public function cancelled()
    {
        return redirect()->route('filament.panel.resources.bookings.index')
            ->with('info', 'Pembayaran dibatalkan');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::id() !== $booking->customer_id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:expired',
            'payment_status' => 'required|in:cancelled'
        ]);

        $booking->update([
            'status' => $validated['status'],
            'payment_status' => $validated['payment_status']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
}
