<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function create(Bus $bus)
    {
        // Cek ketersediaan bus
        $today = Carbon::today();
        if (!$bus->isAvailableOn($today)) {
            return redirect()->back()->with('error', 'Bus tidak tersedia pada tanggal yang dipilih');
        }

        return view('booking.create', compact('bus'));
    }

    public function store(Request $request, Bus $bus)
    {
        $validated = $request->validate([
            'booking_date' => 'required|date|after:today',
            'return_date' => 'required|date|after:booking_date',
            'pickup_location' => 'required|string',
            'destination' => 'required|string',
            'total_seats' => 'required|integer|min:1|max:' . $bus->default_seat_capacity,
            'seat_type' => 'required|in:standard,legrest',
            'special_requests' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Hitung durasi sewa dalam hari
            $startDate = Carbon::parse($validated['booking_date']);
            $endDate = Carbon::parse($validated['return_date']);
            $days = $startDate->diffInDays($endDate) + 1;

            // Cek ulang ketersediaan bus
            if (!$bus->isAvailableOn($startDate, $endDate)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Bus sudah dibooking untuk tanggal tersebut');
            }

            // Buat booking record
            $booking = Booking::create([
                'customer_id' => Auth::id(),
                'bus_id' => $bus->id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'total_amount' => $bus->calculateTotalPrice(
                    $validated['total_seats'],
                    $validated['seat_type'],
                    $days
                ),
                ...$validated
            ]);

            // Generate Midtrans payment
            try {
                $result = $booking->createMidtransPayment();

                if (!$result) {
                    throw new \Exception('Failed to create payment token');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Payment creation failed:', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal membuat pembayaran: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('payment.checkout', $booking)
                ->with('success', 'Booking berhasil dibuat. Silahkan lakukan pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat booking: ' . $e->getMessage());
        }
    }
}
