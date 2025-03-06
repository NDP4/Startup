<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeatConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SeatConfigurationController extends Controller
{
    public function index()
    {
        $configurations = SeatConfiguration::with('bus')->get();
        return response()->json(['success' => true, 'data' => $configurations]);
    }

    public function store(Request $request)
    {
        // Only admin can create seat configurations
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'bus_id' => 'required|exists:buses,id',
            'seat_type' => 'required|in:standard,legrest',
            'number_of_seats' => 'required|integer|min:1',
            'price_per_seat' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $configuration = SeatConfiguration::create($request->all());
        return response()->json(['success' => true, 'data' => $configuration], 201);
    }

    public function show(SeatConfiguration $seatConfiguration)
    {
        return response()->json([
            'success' => true,
            'data' => $seatConfiguration->load('bus')
        ]);
    }

    public function update(Request $request, SeatConfiguration $seatConfiguration)
    {
        // Only admin can update seat configurations
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'seat_type' => 'sometimes|required|in:standard,legrest',
            'number_of_seats' => 'sometimes|required|integer|min:1',
            'price_per_seat' => 'sometimes|required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $seatConfiguration->update($request->all());
        return response()->json(['success' => true, 'data' => $seatConfiguration]);
    }

    public function destroy(SeatConfiguration $seatConfiguration)
    {
        // Only admin can delete seat configurations
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $seatConfiguration->delete();
        return response()->json([
            'success' => true,
            'message' => 'Seat configuration deleted successfully'
        ]);
    }
}
