<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ValidateBookingAvailability
{
    public function handle(Request $request, Closure $next)
    {
        $bus = $request->route('bus');
        $bookingDate = $request->input('booking_date');
        $returnDate = $request->input('return_date');

        if ($bookingDate) {
            $startDate = Carbon::parse($bookingDate);
            $endDate = $returnDate ? Carbon::parse($returnDate) : null;

            if (!$bus->isAvailableOn($startDate, $endDate)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Sorry, this bus is not available for the selected dates.');
            }
        }

        return $next($request);
    }
}
