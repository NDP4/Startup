<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CrewAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrewAssignmentController extends Controller
{
    public function index()
    {
        $assignments = CrewAssignment::whereHas('booking', function ($query) {
            $query->where('customer_id', Auth::id());
        })
            ->with(['booking', 'crew'])
            ->latest()
            ->paginate(10);

        return view('customer.crew-assignments.index', compact('assignments'));
    }

    public function show(CrewAssignment $assignment)
    {
        // Verify access
        if ($assignment->booking->customer_id !== Auth::id()) {
            abort(403);
        }

        $assignment->load(['booking', 'crew']);

        return view('customer.crew-assignments.show', compact('assignment'));
    }
}
