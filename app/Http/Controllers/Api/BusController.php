<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusController extends Controller
{
    public function index()
    {
        try {
            $buses = Bus::all();
            return response()->json([
                'success' => true,
                'message' => 'Data bus berhasil diambil',
                'data' => $buses,
                'total' => $buses->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data bus',
                'error' => $e->getMessage()
            ], 500);
        }
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
                'data' => $bus
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
                'data' => $bus
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
                'data' => $bus
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
