<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Field; // <-- 1. Kita panggil Model Field di sini
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Services\BookingService;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    // Ini pintu yang tadi hilang, Captain!
    public function checkout($booking_id)
    {
        // Kita ambil data booking beserta data gedungnya (venue)
        $booking = Booking::with('venue')->findOrFail($booking_id);

        // Kita arahkan ke file resources/views/user/checkout.blade.php
        return view('user.checkout', compact('booking'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'venue_id' => 'required|exists:venues,id',
            'field_id' => 'required|exists:fields,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        // 2. Ambil Setting Venue
        $venue = Venue::findOrFail($request->venue_id);

        // 3. Ambil Data Lapangan secara Spesifik dari Database
        $field = Field::findOrFail($request->field_id);

        // 4. Hitung Breakdown Biaya via Service secara Dinamis
        // Mengambil harga asli dari kolom database, bukan di-hardcode lagi!
        $basePrice = $field->price; 
        $calculation = $this->bookingService->calculateTotal($basePrice, $venue);

        // 5. Simpan ke Database
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'venue_id' => $request->venue_id,
            'field_id' => $field->id, // <-- Sekarang aman, variabel $field sudah terdefinisi di atas!
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            
            'base_price' => $calculation['base_price'],
            'app_fee' => $calculation['app_fee'],
            'app_fee_bearer' => $calculation['app_fee_bearer'],
            'pg_fee' => $calculation['pg_fee'],
            'pg_fee_bearer' => $calculation['pg_fee_bearer'],
            'total_amount' => $calculation['total_user_pay'],
            'net_profit_owner' => $calculation['net_to_owner'],
            
            'status' => 'pending',
        ]);

        return redirect()->route('checkout.show', $booking->id);
    }
}