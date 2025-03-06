<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'phone' => 'required',
            'address' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 400);
        }

        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['role'] = 'customer';
        $user = User::create($data);
        $token = $user->createToken('authToken')->plainTextToken;

        // mengatur lama penyimpanan token
        $personalAccessToken = $user->tokens()->latest()->first();
        $personalAccessToken->expires_at = Carbon::now()->addWeeks(1);
        $personalAccessToken->save();

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => $user->name,
                'email' => $user->email,
                'token' => $token
            ]
        ], 200);
    }

    function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('authToken')->plainTextToken;
            // mengatur lama penyimpanan token
            $personalAccessToken = $user->tokens()->latest()->first();
            $personalAccessToken->expires_at = Carbon::now()->addWeeks(1);
            $personalAccessToken->save();

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $user->name,
                    'email' => $user->email,
                    'token' => $token
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal',
            ], 401);
        }
    }

    function detail(Request $request, $id = null)
    {
        try {
            $authUser = Auth::user();

            // Jika ID tidak diberikan, tampilkan data user yang login
            if (is_null($id)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Detail user berhasil diambil',
                    'data' => $authUser
                ], 200);
            }

            // Jika bukan admin dan mencoba akses data user lain
            if ($authUser->role !== 'admin' && $authUser->id != $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melihat data user lain'
                ], 403);
            }

            // Cari user yang diminta
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => "User dengan ID {$id} tidak ditemukan"
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail user berhasil diambil',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Tambahkan method untuk admin melihat semua users
    function index()
    {
        try {
            if (Auth::user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $users = User::all();
            return response()->json([
                'success' => true,
                'message' => 'Data users berhasil diambil',
                'data' => $users,
                'total' => $users->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Revoke current user token
            $request->user()->currentAccessToken()->delete();

            // Optional: Revoke semua tokens dari user, jika Anda ingin logout dari semua perangkat
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
