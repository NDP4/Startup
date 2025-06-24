<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index()
    {
        $query = Payment::with(['booking.customer', 'booking.bus']);

        // Filter for customer role
        if (Auth::user()->role === 'customer') {
            $query->whereHas('booking', function ($q) {
                $q->where('customer_id', Auth::id());
            });
        }

        $payments = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pembayaran berhasil diambil',
            'data' => $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'booking_id' => $payment->booking_id,
                    'payment_id' => $payment->payment_id,
                    'amount' => (float) $payment->amount,
                    'payment_type' => $payment->payment_type,
                    'status' => $payment->status,
                    'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $payment->updated_at->format('Y-m-d H:i:s'),
                    'payment_details' => $payment->payment_details,
                    'booking' => [
                        'id' => $payment->booking->id,
                        'customer_name' => $payment->booking->customer->name,
                        'bus_name' => $payment->booking->bus->name,
                        'booking_date' => $payment->booking->booking_date->format('Y-m-d H:i:s'),
                        'return_date' => $payment->booking->return_date?->format('Y-m-d H:i:s'),
                        'total_amount' => (float) $payment->booking->total_amount,
                        'status' => $payment->booking->status,
                    ]
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric',
            'payment_type' => 'required|string',
            'payment_details' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::find($request->booking_id);
        if ($booking->customer_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Jika payment_type midtrans dan snap_token masih null, generate snap_token
        if ($request->payment_type === 'midtrans' && empty($booking->snap_token)) {
            $paymentResult = $booking->createMidtransPayment();
            if (!$paymentResult['success'] || empty($paymentResult['token'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment initialization failed: ' . ($paymentResult['message'] ?? 'Unknown error')
                ], 500);
            }
            $booking->snap_token = $paymentResult['token'];
            if (isset($paymentResult['order_id'])) {
                $booking->order_id = $paymentResult['order_id'];
            }
            $booking->save();
        }

        // Buat record payment jika belum ada (optional, tergantung kebutuhan)
        $payment = Payment::create([
            'booking_id' => $request->booking_id,
            'payment_id' => 'PAY-' . uniqid(),
            'amount' => $request->amount,
            'payment_type' => $request->payment_type,
            'status' => 'pending',
            'payment_details' => $request->payment_details ?? [],
            'created_at' => Carbon::now('Asia/Jakarta'),
            'paid_at' => null
        ]);

        // Return payment_url jika payment_type midtrans
        if ($request->payment_type === 'midtrans') {
            return response()->json([
                'success' => true,
                'message' => 'Payment initialized',
                'data' => [
                    'payment' => [
                        'snap_token' => $booking->snap_token,
                        'payment_url' => (config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/v2/vtweb/' : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/') . $booking->snap_token
                    ]
                ]
            ], 201);
        }

        // Untuk manual, return data payment biasa
        return response()->json(['success' => true, 'data' => $payment], 201);
    }

    public function show(Payment $payment)
    {
        // Check if current user has access to this payment
        if (Auth::user()->role === 'customer' && $payment->booking->customer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses',
            ], 403);
        }

        $payment->load(['booking.customer', 'booking.bus']);

        return response()->json([
            'success' => true,
            'message' => 'Data pembayaran berhasil diambil',
            'data' => [
                'id' => $payment->id,
                'booking_id' => $payment->booking_id,
                'payment_id' => $payment->payment_id,
                'amount' => (float) $payment->amount,
                'payment_type' => $payment->payment_type,
                'status' => $payment->status,
                'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $payment->updated_at->format('Y-m-d H:i:s'),
                'payment_details' => $payment->payment_details,
                'booking' => [
                    'id' => $payment->booking->id,
                    'customer_name' => $payment->booking->customer->name,
                    'bus_name' => $payment->booking->bus->name,
                    'booking_date' => $payment->booking->booking_date->format('Y-m-d H:i:s'),
                    'return_date' => $payment->booking->return_date?->format('Y-m-d H:i:s'),
                    'total_amount' => (float) $payment->booking->total_amount,
                    'status' => $payment->booking->status,
                ]
            ]
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        // Hanya admin yang dapat update status pembayaran.
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,paid,failed',
            'payment_details' => 'sometimes|required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $payment->fill([
            'status' => $request->status,
            'payment_details' => $request->payment_details ?? $payment->getAttribute('payment_details'),
            'paid_at' => $request->status === 'paid' ? Carbon::now('Asia/Jakarta') : null
        ])->save();

        return response()->json(['success' => true, 'data' => $payment]);
    }

    public function destroy(Payment $payment)
    {
        // Hanya admin yang dapat menghapus pembayaran.
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $payment->delete();
        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully'
        ]);
    }
}
