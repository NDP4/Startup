<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CrewAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CrewAssignmentController extends Controller
{
    public function index()
    {
        $assignments = CrewAssignment::with(['booking', 'crew'])->get();
        return response()->json(['success' => true, 'data' => $assignments]);
    }

    public function store(Request $request)
    {
        // Only admin can create assignments
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'crew_id' => 'required|exists:users,id',
            'status' => 'required|in:assigned,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $assignment = CrewAssignment::create($request->all());
        return response()->json(['success' => true, 'data' => $assignment], 201);
    }

    public function show(CrewAssignment $crewAssignment)
    {
        return response()->json([
            'success' => true,
            'data' => $crewAssignment->load(['booking', 'crew'])
        ]);
    }

    public function update(Request $request, CrewAssignment $crewAssignment)
    {
        // Only admin or assigned crew can update
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->id !== $crewAssignment->getAttribute('crew_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|in:assigned,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $crewAssignment->update($request->only(['status', 'notes']));
        return response()->json(['success' => true, 'data' => $crewAssignment]);
    }

    public function destroy(CrewAssignment $crewAssignment)
    {
        // Only admin can delete assignments
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $crewAssignment->delete();
        return response()->json([
            'success' => true,
            'message' => 'Crew assignment deleted successfully'
        ]);
    }
}
