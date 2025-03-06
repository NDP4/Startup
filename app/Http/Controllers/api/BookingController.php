<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['customer', 'bus'])->get();
        return response()->json(['success' => true, 'data' => $bookings]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bus_id' => 'required|exists:buses,id',
            'booking_date' => 'required|date',
            'return_date' => 'required|date|after:booking_date',
            'total_seats' => 'required|integer|min:1',
            'seat_type' => 'required|in:standard,legrest',
            'pickup_location' => 'required',
            'destination' => 'required',
            'special_requests' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['customer_id'] = Auth::id();
        $data['status'] = 'pending';
        $data['payment_status'] = 'pending';

        $booking = Booking::create($data);
        return response()->json(['success' => true, 'data' => $booking], 201);
    }

    public function show(Booking $booking)
    {
        return response()->json(['success' => true, 'data' => $booking->load(['customer', 'bus'])]);
    }

    public function update(Request $request, Booking $booking)
    {
        // Only allow updates if booking is pending
        if (!$booking->isPending()) {
            return response()->json(['success' => false, 'message' => 'Cannot modify confirmed booking'], 403);
        }

        $validator = Validator::make($request->all(), [
            'special_requests' => 'nullable',
            'status' => 'sometimes|in:cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $booking->update($request->validated());
        return response()->json(['success' => true, 'data' => $booking]);
    }

    public function destroy(Booking $booking)
    {
        if (!$booking->isPending()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete confirmed booking'], 403);
        }

        $booking->delete();
        return response()->json(['success' => true, 'message' => 'Booking cancelled successfully']);
    }
}
