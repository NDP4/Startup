<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::where('customer_id', Auth::id())
            ->with(['booking.bus'])
            ->latest()
            ->paginate(10);

        return view('customer.reviews.index', compact('reviews'));
    }
}
