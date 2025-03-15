<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerProfileController extends Controller
{
    public function edit()
    {
        return view('customer.profile.edit');
    }

    public function update(Request $request)
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        try {
            $user->update($validated);
            return back()->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update profile. Please try again.');
        }
    }
}
