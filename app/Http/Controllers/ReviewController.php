<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\CrewReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;  // Add this import

class ReviewController extends Controller
{
    use AuthorizesRequests;  // Add this trait

    public function create(Booking $booking)
    {
        if (!$booking->canBeReviewed()) {
            return redirect()->route('filament.panel.resources.bookings.index')
                ->with('error', 'Booking ini tidak dapat direview');
        }

        return view('reviews.create', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        $validationRules = [
            'bus_rating' => 'required|integer|min:1|max:5',
            'bus_comment' => 'required|string',
        ];

        // Only validate crew review if there's a crew assigned
        if ($booking->crewAssignments->isNotEmpty()) {
            $validationRules['crew_rating'] = 'required|integer|min:1|max:5';
            $validationRules['crew_comment'] = 'required|string';
        }

        $request->validate($validationRules);

        $reviewData = [
            'booking_id' => $booking->id,
            'customer_id' => Auth::id(),
            'bus_id' => $booking->bus_id,
            'bus_rating' => $request->bus_rating,
            'bus_comment' => $request->bus_comment,
        ];

        // Add crew review data only if crew is assigned
        if ($booking->crewAssignments->isNotEmpty()) {
            $reviewData['crew_id'] = $booking->crewAssignments->first()->crew_id;
            $reviewData['crew_rating'] = $request->crew_rating;
            $reviewData['crew_comment'] = $request->crew_comment;
        }

        Review::create($reviewData);

        return redirect()->route('filament.panel.resources.bookings.index')
            ->with('success', 'Review berhasil dikirim');
    }

    public function edit(Review $review)
    {
        $this->authorize('update', $review);

        if (Auth::id() !== $review->customer_id) {
            return redirect()->route('filament.panel.resources.bookings.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit review ini');
        }

        return view('reviews.edit', [
            'review' => $review->load(['booking', 'customer', 'bus', 'crew'])
        ]);
    }

    public function update(Request $request, Review $review)
    {
        if (Auth::id() !== $review->customer_id) {
            return redirect()->route('filament.panel.resources.bookings.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit review ini');
        }

        $validationRules = [
            'bus_rating' => 'required|integer|min:1|max:5',
            'bus_comment' => 'required|string',
        ];

        // Only validate crew review if there's a crew assigned
        if ($review->crew_id) {
            $validationRules['crew_rating'] = 'required|integer|min:1|max:5';
            $validationRules['crew_comment'] = 'required|string';
        }

        $request->validate($validationRules);

        $review->update($request->all());

        return redirect()->route('filament.panel.resources.bookings.index')
            ->with('success', 'Review berhasil diperbarui');
    }
}
