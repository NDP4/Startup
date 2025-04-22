<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BusController extends Controller
{
    public function index(Request $request)
    {
        $query = Bus::query();

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%")
                    ->orWhere('number_plate', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('pricing_type')) {
            $query->where('pricing_type', $request->pricing_type);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('price_per_day', '>=', $request->min_price)
                    ->orWhere('price_per_km', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('price_per_day', '<=', $request->max_price)
                    ->orWhere('price_per_km', '<=', $request->max_price);
            });
        }

        // Check availability for specific date
        if ($request->filled(['booking_date', 'return_date'])) {
            $startDate = Carbon::parse($request->booking_date);
            $endDate = Carbon::parse($request->return_date);

            $query->whereDoesntHave('bookings', function ($q) use ($startDate, $endDate) {
                $q->where('payment_status', 'paid')
                    ->where(function ($sq) use ($startDate, $endDate) {
                        $sq->whereBetween('booking_date', [$startDate, $endDate])
                            ->orWhereBetween('return_date', [$startDate, $endDate])
                            ->orWhere(function ($innerQ) use ($startDate, $endDate) {
                                $innerQ->where('booking_date', '<=', $startDate)
                                    ->where('return_date', '>=', $endDate);
                            });
                    });
            });
        }

        // Sort
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $buses = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $buses,
            'message' => 'Bus list retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'number_plate' => 'required|unique:buses',
            'description' => 'nullable',
            'default_seat_capacity' => 'required|integer',
            'status' => 'required|in:available,maintenance,booked',
            'pricing_type' => 'required|in:daily,distance',
            'price_per_day' => 'required_if:pricing_type,daily',
            'price_per_km' => 'required_if:pricing_type,distance',
            'images' => 'nullable|array',
            'images.*.url' => 'required|string',
            'images.*.description' => 'nullable|string'
        ], [
            'required' => ':attribute harus diisi',
            'unique' => ':attribute sudah digunakan',
            'integer' => ':attribute harus berupa angka',
            'in' => ':attribute tidak valid',
            'required_if' => ':attribute harus diisi ketika tipe harga adalah :other'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bus = Bus::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Bus berhasil ditambahkan',
                'data' => [
                    'id' => $bus->id,
                    'name' => $bus->name,
                    'number_plate' => $bus->number_plate,
                    'description' => $bus->description,
                    'default_seat_capacity' => $bus->default_seat_capacity,
                    'status' => $bus->status,
                    'pricing_type' => $bus->pricing_type,
                    'price_per_day' => $bus->price_per_day,
                    'price_per_km' => $bus->price_per_km,
                    'images' => $bus->all_images,
                    'main_image' => $bus->main_image
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan bus',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $bus = Bus::find($id);

            if (!$bus) {
                return response()->json([
                    'success' => false,
                    'message' => "Bus dengan ID {$id} tidak ditemukan"
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data bus ditemukan',
                'data' => [
                    'id' => $bus->id,
                    'name' => $bus->name,
                    'number_plate' => $bus->number_plate,
                    'description' => $bus->description,
                    'default_seat_capacity' => $bus->default_seat_capacity,
                    'status' => $bus->status,
                    'pricing_type' => $bus->pricing_type,
                    'price_per_day' => $bus->price_per_day,
                    'price_per_km' => $bus->price_per_km,
                    'images' => $bus->all_images,
                    'main_image' => $bus->main_image
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail bus',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Bus $bus)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required',
            'number_plate' => 'sometimes|required|unique:buses,number_plate,' . $bus->getAttribute('id'),
            'description' => 'nullable',
            'default_seat_capacity' => 'sometimes|required|integer',
            'status' => 'sometimes|required|in:available,maintenance,booked',
            'pricing_type' => 'sometimes|required|in:daily,distance',
            'price_per_day' => 'required_if:pricing_type,daily',
            'price_per_km' => 'required_if:pricing_type,distance',
            'images' => 'nullable|array',
            'images.*.url' => 'required|string',
            'images.*.description' => 'nullable|string'
        ], [
            'required' => ':attribute harus diisi',
            'unique' => ':attribute sudah digunakan',
            'integer' => ':attribute harus berupa angka',
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
            $bus->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Bus berhasil diperbarui',
                'data' => [
                    'id' => $bus->id,
                    'name' => $bus->name,
                    'number_plate' => $bus->number_plate,
                    'description' => $bus->description,
                    'default_seat_capacity' => $bus->default_seat_capacity,
                    'status' => $bus->status,
                    'pricing_type' => $bus->pricing_type,
                    'price_per_day' => $bus->price_per_day,
                    'price_per_km' => $bus->price_per_km,
                    'images' => $bus->all_images,
                    'main_image' => $bus->main_image
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui bus',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Bus $bus)
    {
        try {
            $bus->delete();
            return response()->json([
                'success' => true,
                'message' => 'Bus berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus bus',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
