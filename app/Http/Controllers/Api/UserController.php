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
            'address' => 'required',
            'role' => 'required'
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
}
