<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingHistoryController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = Auth::id();
        
        // 🔍 Ambil filter dari request (Pencarian & Status)
        $search = $request->input('search');
        $status = $request->input('status');

        // Query dasar mengambil data booking milik venue si owner
        $query = Booking::with(['user', 'field', 'venue','details'])
            ->whereHas('venue', function ($q) use ($ownerId) {
                $q->where('user_id', $ownerId);
            });

        // 🔎 Filter Pencarian (Berdasarkan Kode Booking ATAU Nama Customer)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('booking_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // 🚦 Filter Status Booking
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Ambil data terbaru dengan sistem penomoran halaman (Pagination) agar performa tetap ringan
        $bookings = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.booking.history', compact('bookings', 'search', 'status'));
    }
}