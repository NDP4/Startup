<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index()
    {
        $query = Review::with(['booking.customer', 'bus', 'crew']);
        $user = Auth::user();

        // Filter based on role
        if ($user->role === 'customer') {
            $query->whereHas('booking', function ($q) use ($user) {
                $q->where('customer_id', $user->id);
            });
        } elseif ($user->role === 'crew') {
            $query->where('crew_id', $user->id);
        }

        $reviews = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data review berhasil diambil',
            'meta' => [
                'total' => $reviews->count(),
                'average_rating' => $user->role === 'crew'
                    ? $reviews->avg('crew_rating')
                    : $reviews->avg('bus_rating'),
            ],
            'data' => $reviews->map(function ($review) use ($user) {
                $data = [
                    'id' => $review->id,
                    'booking' => [
                        'id' => $review->booking->id,
                        'customer_name' => $review->booking->customer->name,
                        'booking_date' => $review->booking->booking_date->format('Y-m-d H:i:s'),
                        'pickup_location' => $review->booking->pickup_location,
                        'destination' => $review->booking->destination,
                        'status' => $review->booking->status,
                    ],
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $review->updated_at->format('Y-m-d H:i:s'),
                ];

                // Add specific data based on role
                if (in_array($user->role, ['admin', 'customer'])) {
                    $data['bus'] = [
                        'id' => $review->bus->id,
                        'name' => $review->bus->name,
                        'rating' => $review->bus_rating,
                        'comment' => $review->bus_comment,
                    ];
                }

                if (in_array($user->role, ['admin', 'crew']) && $review->crew) {
                    $data['crew'] = [
                        'id' => $review->crew->id,
                        'name' => $review->crew->name,
                        'rating' => $review->crew_rating,
                        'comment' => $review->crew_comment,
                    ];
                }

                return $data;
            })
        ]);
    }

    public function store(Request $request, Booking $booking)
    {
        if (!$booking->canBeReviewed()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking ini tidak dapat direview'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'bus_rating' => 'required|integer|min:1|max:5',
            'bus_comment' => 'required|string',
            'crew_rating' => $booking->crewAssignments->isNotEmpty() ? 'required|integer|min:1|max:5' : 'nullable',
            'crew_comment' => $booking->crewAssignments->isNotEmpty() ? 'required|string' : 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $reviewData = [
            'booking_id' => $booking->id,
            'customer_id' => Auth::id(),
            'bus_id' => $booking->bus_id,
            'bus_rating' => $request->bus_rating,
            'bus_comment' => $request->bus_comment,
        ];

        if ($booking->crewAssignments->isNotEmpty()) {
            $reviewData['crew_id'] = $booking->crewAssignments->first()->crew_id;
            $reviewData['crew_rating'] = $request->crew_rating;
            $reviewData['crew_comment'] = $request->crew_comment;
        }

        $review = Review::create($reviewData);

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil disimpan',
            'data' => $review
        ], 201);
    }

    public function show(Review $review)
    {
        // Check access
        $user = Auth::user();
        if ($user->role === 'customer' && $review->booking->customer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses',
            ], 403);
        } elseif ($user->role === 'crew' && $review->crew_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses',
            ], 403);
        }

        $review->load(['booking.customer', 'bus', 'crew']);

        return response()->json([
            'success' => true,
            'message' => 'Data review berhasil diambil',
            'data' => [
                'id' => $review->id,
                'booking' => [
                    'id' => $review->booking->id,
                    'customer_name' => $review->booking->customer->name,
                    'booking_date' => $review->booking->booking_date->format('Y-m-d H:i:s'),
                ],
                'bus' => [
                    'id' => $review->bus->id,
                    'name' => $review->bus->name,
                    'rating' => $review->bus_rating,
                    'comment' => $review->bus_comment,
                ],
                'crew' => $review->crew ? [
                    'id' => $review->crew->id,
                    'name' => $review->crew->name,
                    'rating' => $review->crew_rating,
                    'comment' => $review->crew_comment,
                ] : null,
                'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $review->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function update(Request $request, Review $review)
    {
        // Check access
        if (Auth::user()->role !== 'admin' && $review->booking->customer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses untuk mengedit review ini'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'bus_rating' => 'required|integer|min:1|max:5',
            'bus_comment' => 'required|string',
            'crew_rating' => $review->crew_id ? 'required|integer|min:1|max:5' : 'nullable',
            'crew_comment' => $review->crew_id ? 'required|string' : 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $review->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil diperbarui',
            'data' => $review
        ]);
    }

    public function destroy(Review $review)
    {
        // Check access
        if (Auth::user()->role !== 'admin' && $review->booking->customer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses untuk menghapus review ini'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil dihapus'
        ]);
    }
}
