<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function show(Booking $booking)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->id !== $booking->customer_id) {
            abort(403);
        }

        // Eager load payments dengan with() untuk memastikan relasinya ter-load
        $booking->load(['customer', 'bus']);
        $booking->load(['payments' => function ($query) {
            $query->latest('paid_at')->with(['booking']);
        }]);

        return view('receipts.show', compact('booking'));
    }

    public function download(Booking $booking)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->id !== $booking->customer_id) {
            abort(403);
        }

        // Eager load payments dengan with() untuk memastikan relasinya ter-load
        $booking->load(['customer', 'bus']);
        $booking->load(['payments' => function ($query) {
            $query->latest('paid_at')->with(['booking']);
        }]);

        $pdf = Pdf::loadView('receipts.pdf', compact('booking'));

        return $pdf->download("kwitansi-{$booking->id}.pdf");
    }
}
