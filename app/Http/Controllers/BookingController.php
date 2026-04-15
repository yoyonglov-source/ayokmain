<?php

namespace App\Http\Controllers;

use App\Models\Venue;
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

        // 2. Ambil Setting Venue (fee_mode, pg_fee_bearer, dll)
        $venue = Venue::findOrFail($request->venue_id);

        // 3. Cek Ketersediaan Lapangan (Logic ini bisa kita kembangkan nanti)
        // Sementara kita asumsikan tersedia.

        // 4. Hitung Breakdown Biaya via Service
        // Kita gunakan harga dasar dari input atau dari tabel fields
        $basePrice = 50000; // Contoh statis, nanti ambil dari $field->price_per_hour
        $calculation = $this->bookingService->calculateTotal($basePrice, $venue);

        // 5. Simpan ke Database
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'venue_id' => $request->venue_id,
            'field_id' => $request->field_id,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            
            // Data dari hasil hitungan Service
            'base_price' => $calculation['base_price'],
            'app_fee' => $calculation['app_fee'],
            'app_fee_bearer' => $calculation['app_fee_bearer'],
            'pg_fee' => $calculation['pg_fee'],
            'pg_fee_bearer' => $calculation['pg_fee_bearer'],
            'total_amount' => $calculation['total_user_pay'],
            'net_profit_owner' => $calculation['net_to_owner'],
            
            'status' => 'pending',
        ]);

        // 6. Arahkan ke Halaman Pembayaran (Checkout)
        return redirect()->route('checkout.show', $booking->id);
    }
}