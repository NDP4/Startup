<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['booking'])->get();
        return response()->json(['success' => true, 'data' => $payments]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric',
            'payment_type' => 'required|string',
            'payment_details' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Check if booking belongs to authenticated user
        $booking = Booking::find($request->booking_id);
        if ($booking->customer_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $payment = Payment::create([
            'booking_id' => $request->booking_id,
            'payment_id' => 'PAY-' . uniqid(),
            'amount' => $request->amount,
            'payment_type' => $request->payment_type,
            'status' => 'pending',
            'payment_details' => $request->payment_details,
            'paid_at' => null
        ]);

        return response()->json(['success' => true, 'data' => $payment], 201);
    }

    public function show(Payment $payment)
    {
        $booking = $payment->booking()->first();
        if ($booking->customer_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $payment->load('booking')
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        // Only allow admin to update payment status
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
            'paid_at' => $request->status === 'paid' ? now() : null
        ])->save();

        return response()->json(['success' => true, 'data' => $payment]);
    }

    public function destroy(Payment $payment)
    {
        // Only allow admin to delete payments
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
