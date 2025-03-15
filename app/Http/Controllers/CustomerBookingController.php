<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CustomerBookingController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $bookings = Booking::where('customer_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        return view('customer.bookings.show', compact('booking'));
    }
}
