<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingHistoryController extends Controller
{
    public function index()
    {
        // Ambil data booking milik user yang sedang login
        // Urutkan dari yang paling baru dibuat (latest)
        $bookings = Booking::with(['venue', 'bookingDetails.field'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        // Fitur opsional jika user ingin klik detail untuk melihat invoice/e-ticket penuh
        $booking = Booking::with(['venue', 'bookingDetails.field'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.bookings.show', compact('booking'));
    }
}