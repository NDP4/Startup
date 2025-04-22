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
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bus not available for selected dates'
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Bus not available for selected dates');
            }
        }

        return $next($request);
    }
}
