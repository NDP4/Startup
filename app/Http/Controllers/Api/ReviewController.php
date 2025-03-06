<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['customer', 'booking'])->get();
        return response()->json(['success' => true, 'data' => $reviews]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $review = Review::create([
            'customer_id' => Auth::id(),
            'booking_id' => $request->booking_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json(['success' => true, 'data' => $review], 201);
    }

    public function show(Review $review)
    {
        return response()->json([
            'success' => true,
            'data' => $review->load(['customer', 'booking'])
        ]);
    }

    public function update(Request $request, Review $review)
    {
        if ($review->getAttribute('customer_id') !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|required|integer|between:1,5',
            'comment' => 'sometimes|required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $review->update($request->only(['rating', 'comment']));
        return response()->json(['success' => true, 'data' => $review]);
    }

    public function destroy(Review $review)
    {
        if ($review->getAttribute('customer_id') !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $review->delete();
        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    }
}
