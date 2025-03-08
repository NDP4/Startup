<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function checkout(Booking $booking)
    {
        // Authorization check
        if (
            !Auth::check() ||
            (Auth::user()->role !== 'admin' &&
                Auth::id() !== $booking->getAttribute('customer_id'))
        ) {
            abort(403, 'Unauthorized access');
        }

        // Load relationships for the view
        $booking->load(['customer', 'bus']);

        try {
            // Only create new payment if no valid snap token exists
            if (empty($booking->snap_token)) {
                $booking->createMidtransPayment();
                $booking->refresh();
            }

            if (empty($booking->snap_token)) {
                throw new \Exception('Failed to get payment token');
            }
        } catch (\Exception $e) {
            return redirect()->route('filament.panel.resources.bookings.index')
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }

        return view('payment.checkout', compact('booking'));
    }

    public function success(Request $request)
    {
        $orderId = $request->get('order_id');
        $bookingId = str_replace('BOOKING-', '', explode('-', $orderId)[1]);
        $booking = Booking::find($bookingId);

        if ($booking && $request->get('status') === 'settlement') {
            // Extract payment details
            $paymentDetails = $request->all();
            $vaNumbers = $paymentDetails['va_numbers'] ?? [];
            $vaNumber = !empty($vaNumbers) ? $vaNumbers[0]['va_number'] : null;
            $bankName = !empty($vaNumbers) ? strtoupper($vaNumbers[0]['bank']) : null;

            // Format payment type based on Midtrans response
            $paymentMethod = match ($request->get('payment_type')) {
                'bank_transfer' => sprintf(
                    'Virtual Account %s (%s)',
                    $bankName,
                    $vaNumber
                ),
                'credit_card' => $request->get('card_type', 'Kartu Kredit'),
                'gopay' => 'GoPay',
                'qris' => 'QRIS',
                default => $request->get('payment_type', 'Unknown')
            };

            // Create payment record dengan detail lengkap
            $booking->payments()->create([
                'payment_id' => $request->get('transaction_id'),
                'amount' => $booking->total_amount,
                'payment_type' => $paymentMethod,
                'status' => 'success',
                'payment_details' => [
                    'transaction_id' => $request->get('transaction_id'),
                    'order_id' => $orderId,
                    'status' => $request->get('status'),
                    'payment_type' => $paymentMethod,
                    'payment_method' => $request->get('payment_type'),
                    'va_numbers' => $vaNumbers,
                    'bank' => $bankName,
                    'va_number' => $vaNumber,
                    'acquirer' => $request->get('acquirer'),
                    'settlement_time' => $request->get('settlement_time'),
                    'transaction_time' => $request->get('transaction_time'),
                    'currency' => $request->get('currency', 'IDR'),
                    'fraud_status' => $request->get('fraud_status'),
                ],
                'paid_at' => $request->get('settlement_time')
                    ? date('Y-m-d H:i:s', strtotime($request->get('settlement_time')))
                    : now()
            ]);

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
}
