<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Bus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index()
    {
        $query = Booking::with(['customer', 'bus', 'payments']);

        // Filter for customer role
        if (Auth::user()->role === 'customer') {
            $query->where('customer_id', Auth::id());
        }

        $bookings = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data booking berhasil diambil',
            'data' => $bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'customer' => [
                        'id' => $booking->customer->id,
                        'name' => $booking->customer->name,
                        'email' => $booking->customer->email,
                        'phone' => $booking->customer->phone,
                    ],
                    'bus' => [
                        'id' => $booking->bus->id,
                        'name' => $booking->bus->name,
                        'number_plate' => $booking->bus->number_plate,
                    ],
                    'booking_date' => $booking->booking_date->format('Y-m-d H:i:s'),
                    'return_date' => $booking->return_date?->format('Y-m-d H:i:s'),
                    'pickup_location' => $booking->pickup_location,
                    'destination' => $booking->destination,
                    'total_seats' => $booking->total_seats,
                    'seat_type' => $booking->seat_type,
                    'total_amount' => (float) $booking->total_amount,
                    'special_requests' => $booking->special_requests,
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'snap_token' => $booking->snap_token,
                    'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $booking->updated_at->format('Y-m-d H:i:s'),
                    'latest_payment' => $booking->payments->last() ? [
                        'id' => $booking->payments->last()->id,
                        'payment_id' => $booking->payments->last()->payment_id,
                        'amount' => (float) $booking->payments->last()->amount,
                        'payment_type' => $booking->payments->last()->payment_type,
                        'status' => $booking->payments->last()->status,
                        'paid_at' => $booking->payments->last()->paid_at?->format('Y-m-d H:i:s'),
                    ] : null,
                ];
            })
        ]);
    }

    public function store(Request $request, Bus $bus)
    {
        $validator = Validator::make($request->all(), [
            'booking_date' => 'required|date|after:today',
            'return_date' => 'required|date|after:booking_date',
            'pickup_location' => 'required|string',
            'destination' => 'required|string',
            'total_seats' => 'required|integer|min:1|max:' . $bus->default_seat_capacity,
            'seat_type' => 'required|in:standard,legrest',
            'special_requests' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $startDate = Carbon::parse($request->booking_date);
            $endDate = Carbon::parse($request->return_date);
            $days = $startDate->diffInDays($endDate) + 1;

            if (!$bus->isAvailableOn($startDate, $endDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bus not available for selected dates'
                ], 422);
            }

            $booking = Booking::create([
                'customer_id' => Auth::id(),
                'bus_id' => $bus->id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'total_amount' => $bus->calculateTotalPrice(
                    $request->total_seats,
                    $request->seat_type,
                    $days
                ),
                ...$validator->validated()
            ]);

            try {
                $paymentResult = $booking->createMidtransPayment();

                if (!$paymentResult['success']) {
                    throw new \Exception('Failed to create payment');
                }

                $booking->snap_token = $paymentResult['token'];
                $booking->save();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment initialization failed: ' . $e->getMessage()
                ], 500);
            }

            DB::commit();

            // Load relationships for complete booking data
            $booking->load(['customer', 'bus']);

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'data' => [
                    'booking' => [
                        'id' => $booking->id,
                        'customer' => [
                            'id' => $booking->customer->id,
                            'name' => $booking->customer->name,
                            'email' => $booking->customer->email,
                            'phone' => $booking->customer->phone,
                        ],
                        'bus' => [
                            'id' => $booking->bus->id,
                            'name' => $booking->bus->name,
                            'number_plate' => $booking->bus->number_plate,
                        ],
                        'booking_date' => $booking->booking_date->format('Y-m-d H:i:s'),
                        'return_date' => $booking->return_date?->format('Y-m-d H:i:s'),
                        'pickup_location' => $booking->pickup_location,
                        'destination' => $booking->destination,
                        'total_seats' => $booking->total_seats,
                        'seat_type' => $booking->seat_type,
                        'total_amount' => (float) $booking->total_amount,
                        'special_requests' => $booking->special_requests,
                        'status' => $booking->status,
                        'payment_status' => $booking->payment_status,
                        'snap_token' => $booking->snap_token,
                        'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $booking->updated_at->format('Y-m-d H:i:s'),
                    ],
                    'payment' => [
                        'snap_token' => $booking->snap_token,
                        'payment_url' => config('services.midtrans.is_production')
                            ? 'https://app.midtrans.com/snap/v2/vtweb/'
                            : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/'
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Booking creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Booking $booking)
    {
        // Check if current user has access to this booking
        if (Auth::user()->role === 'customer' && $booking->customer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses',
            ], 403);
        }

        $booking->load(['customer', 'bus', 'payments', 'crewAssignments.crew']);

        return response()->json([
            'success' => true,
            'message' => 'Data booking berhasil diambil',
            'data' => [
                'id' => $booking->id,
                'customer' => [
                    'id' => $booking->customer->id,
                    'name' => $booking->customer->name,
                    'email' => $booking->customer->email,
                    'phone' => $booking->customer->phone,
                ],
                'bus' => [
                    'id' => $booking->bus->id,
                    'name' => $booking->bus->name,
                    'number_plate' => $booking->bus->number_plate,
                    'pricing_type' => $booking->bus->pricing_type,
                    'price_per_day' => (float) $booking->bus->price_per_day,
                    'price_per_km' => (float) $booking->bus->price_per_km,
                    'legrest_price_per_seat' => (float) $booking->bus->legrest_price_per_seat,
                ],
                'booking_date' => $booking->booking_date->format('Y-m-d H:i:s'),
                'return_date' => $booking->return_date?->format('Y-m-d H:i:s'),
                'pickup_location' => $booking->pickup_location,
                'destination' => $booking->destination,
                'total_seats' => $booking->total_seats,
                'seat_type' => $booking->seat_type,
                'total_amount' => (float) $booking->total_amount,
                'special_requests' => $booking->special_requests,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'snap_token' => $booking->snap_token,
                'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $booking->updated_at->format('Y-m-d H:i:s'),
                'crew_assignments' => $booking->crewAssignments->map(function ($assignment) {
                    return [
                        'id' => $assignment->id,
                        'crew' => [
                            'id' => $assignment->crew->id,
                            'name' => $assignment->crew->name,
                            'phone' => $assignment->crew->phone,
                        ],
                        'status' => $assignment->status,
                        'notes' => $assignment->notes,
                    ];
                }),
                'payments' => $booking->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'payment_id' => $payment->payment_id,
                        'amount' => (float) $payment->amount,
                        'payment_type' => $payment->payment_type,
                        'status' => $payment->status,
                        'payment_details' => $payment->payment_details,
                        'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                        'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
            ]
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        // Cek kepemilikan booking
        if (Auth::user()->role !== 'admin' && $booking->customer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke pemesanan ini'
            ], 403);
        }

        // Hanya admin yang bisa mengubah booking yang sudah dibayar
        if (Auth::user()->role !== 'admin' && !$booking->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengubah pemesanan yang sudah dikonfirmasi'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'special_requests' => 'nullable',
            'status' => 'sometimes|in:cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $booking->update($request->validated());
            return response()->json([
                'success' => true,
                'data' => $booking
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pemesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Booking $booking)
    {
        // Cek kepemilikan booking
        if (Auth::user()->role !== 'admin' && $booking->customer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke pemesanan ini'
            ], 403);
        }

        if (!$booking->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus pemesanan yang sudah dikonfirmasi'
            ], 403);
        }

        try {
            $booking->delete();
            return response()->json([
                'success' => true,
                'message' => 'Pemesanan berhasil dibatalkan'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pemesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
