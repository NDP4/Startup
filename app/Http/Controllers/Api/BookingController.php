<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $bookings = Booking::query()
                ->when($user->role !== 'admin', function ($query) use ($user) {
                    $query->where('customer_id', $user->id);
                })
                ->with(['customer', 'bus'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data pemesanan berhasil diambil',
                'data' => $bookings,
                'total' => $bookings->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pemesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bus_id' => 'required|exists:buses,id',
            'booking_date' => 'required|date',
            'return_date' => 'required|date|after:booking_date',
            'total_seats' => 'required|integer|min:1',
            'seat_type' => 'required|in:standard,legrest',
            'pickup_location' => 'required',
            'destination' => 'required',
            'special_requests' => 'nullable'
        ], [
            'required' => ':attribute harus diisi',
            'exists' => ':attribute tidak ditemukan',
            'date' => ':attribute harus berupa tanggal',
            'after' => ':attribute harus setelah tanggal pemesanan',
            'integer' => ':attribute harus berupa angka',
            'min' => ':attribute minimal :min',
            'in' => ':attribute tidak valid'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            $data['customer_id'] = Auth::id();
            $data['status'] = 'pending';
            $data['payment_status'] = 'pending';

            $booking = Booking::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Pemesanan berhasil dibuat',
                'data' => $booking
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pemesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $booking = Booking::with(['customer', 'bus'])->find($id);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => "Data pemesanan dengan ID {$id} tidak ditemukan"
                ], 404);
            }

            // Cek apakah user memiliki akses ke booking ini
            if (Auth::user()->role !== 'admin' && $booking->customer_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke pemesanan ini'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data pemesanan ditemukan',
                'data' => $booking
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pemesanan',
                'error' => $e->getMessage()
            ], 500);
        }
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

        if (!$booking->isPending()) {
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
