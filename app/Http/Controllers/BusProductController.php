<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class BusProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Bus::query();

        // Apply filters
        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%");
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('min_capacity')) {
            $query->where('default_seat_capacity', '>=', $request->min_capacity);
        }

        if ($request->has('max_capacity')) {
            $query->where('default_seat_capacity', '<=', $request->max_capacity);
        }

        if ($request->has('pricing_type')) {
            $query->where('pricing_type', $request->pricing_type);
        }

        // Price range filter
        if ($request->has('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price)
                ->orWhere('price_per_km', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price)
                ->orWhere('price_per_km', '<=', $request->max_price);
        }

        // Check availability for specific date
        if ($request->has('booking_date')) {
            $date = $request->booking_date;
            $query->whereDoesntHave('bookings', function ($q) use ($date) {
                $q->where('booking_date', '<=', $date)
                    ->where('return_date', '>=', $date)
                    ->whereIn('status', ['confirmed', 'completed']);
            });
        }

        // Filter berdasarkan tanggal booking
        if ($request->has('booking_date')) {
            $bookingDate = Carbon::parse($request->booking_date);
            $returnDate = $request->has('return_date') ? Carbon::parse($request->return_date) : $bookingDate;

            $query->whereDoesntHave('bookings', function ($q) use ($bookingDate, $returnDate) {
                $q->where('payment_status', 'paid')
                    ->where(function ($sq) use ($bookingDate, $returnDate) {
                        $sq->where(function ($innerQ) use ($bookingDate, $returnDate) {
                            $innerQ->whereBetween('booking_date', [$bookingDate, $returnDate])
                                ->orWhereBetween('return_date', [$bookingDate, $returnDate])
                                ->orWhere(function ($deepQ) use ($bookingDate, $returnDate) {
                                    $deepQ->where('booking_date', '<=', $bookingDate)
                                        ->where('return_date', '>=', $returnDate);
                                });
                        });
                    });
            });
        }

        // Filter berdasarkan tanggal booking
        if ($request->has('booking_date')) {
            $bookingDate = Carbon::parse($request->booking_date);
            $returnDate = $request->has('return_date') ? Carbon::parse($request->return_date) : $bookingDate;

            $query->whereDoesntHave('bookings', function ($q) use ($bookingDate, $returnDate) {
                $q->where('payment_status', 'paid')
                    ->where(function ($sq) use ($bookingDate, $returnDate) {
                        $sq->whereBetween('booking_date', [$bookingDate, $returnDate])
                            ->orWhereBetween('return_date', [$bookingDate, $returnDate])
                            ->orWhere(function ($innerQ) use ($bookingDate, $returnDate) {
                                $innerQ->where('booking_date', '<=', $bookingDate)
                                    ->where('return_date', '>=', $returnDate);
                            })
                            ->orWhere(function ($innerQ) use ($bookingDate, $returnDate) {
                                $innerQ->where('booking_date', '<=', $returnDate)
                                    ->where('return_date', '>=', $bookingDate);
                            });
                    });
            });
        }

        // Sort
        $sort = $request->sort ?? 'price_asc';
        switch ($sort) {
            case 'price_desc':
                $query->orderByRaw('COALESCE(price_per_day, price_per_km) DESC');
                break;
            case 'price_asc':
                $query->orderByRaw('COALESCE(price_per_day, price_per_km) ASC');
                break;
            case 'capacity_desc':
                $query->orderBy('default_seat_capacity', 'desc');
                break;
            case 'capacity_asc':
                $query->orderBy('default_seat_capacity', 'asc');
                break;
        }

        // Get the paginated results and transform them
        $perPage = 12;
        $page = request()->get('page', 1);
        $buses = $query->get();

        // Transform the results
        $transformedBuses = $buses->map(function ($bus) use ($request) {
            $bus->is_available = true;
            if ($request->has('booking_date')) {
                $startDate = Carbon::parse($request->booking_date);
                $endDate = $request->has('return_date') ? Carbon::parse($request->return_date) : null;
                $bus->is_available = $bus->isAvailableOn($startDate, $endDate);
            }
            return $bus;
        });

        // Manual pagination
        $paginatedBuses = new LengthAwarePaginator(
            $transformedBuses->forPage($page, $perPage),
            $transformedBuses->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('buses.index', ['buses' => $paginatedBuses]);
    }

    public function show(Bus $bus)
    {
        $bus->load(['reviews' => function ($query) {
            $query->with('customer')
                ->latest()
                ->limit(5);
        }]);

        $averageRating = $bus->reviews->avg('bus_rating');
        $totalReviews = $bus->reviews->count();

        return view('buses.show', compact('bus', 'averageRating', 'totalReviews'));
    }
}
