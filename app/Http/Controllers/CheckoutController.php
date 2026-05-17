<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Field;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi request kiriman dari Alpine.js
        $request->validate([
            'booking_date' => 'required|date',
            'slots' => 'required|array|min:1',
        ]);

        // Ambil data slot pertama untuk mendeteksi venue_id (induk gedung)
        $firstSlot = $request->slots[0];
        $field = Field::findOrFail($firstSlot['fieldId']);
        $venueId = $field->venue_id;

        // Gunakan Database Transaction agar jika salah satu detail gagal tersimpan, 
        // seluruh data booking otomatis dibatalkan (Menghindari data sampah)
        DB::beginTransaction();

        try {
            // 2. Kalkulasi Total Harga murni dari data slot backend
            $basePrice = 0;
            foreach ($request->slots as $slot) {
                $basePrice += intval($slot['price']);
            }

            // Simulasi dummy biaya admin & PG (nanti disesuaikan dengan skema Xendit)
            $appFee = 2000; 
            $pgFee = 3000;
            $totalAmount = $basePrice + $appFee + $pgFee;

            // 3. Buat data Booking Utama (Induk)
            $booking = Booking::create([
                'user_id'          => Auth::id(),
                'venue_id'         => $venueId,
                'booking_date'     => $request->booking_date,
                'base_price'       => $basePrice,
                'app_fee'          => $appFee,
                'app_fee_bearer'   => 'user', // user yang menanggung biaya
                'pg_fee'           => $pgFee,
                'pg_fee_bearer'    => 'user',
                'payment_method'   => 'PENDING_SIMULATION', // Metode sementara
                'total_amount'     => $totalAmount,
                'net_profit_owner' => $basePrice, // Keuntungan bersih owner GOR
                'status'           => 'pending',  // Status awal wajib pending
            ]);

            // 4. Loop dan masukkan tiap slot jam ke tabel BookingDetail
            foreach ($request->slots as $slot) {
                BookingDetail::create([
                    'booking_id' => $booking->id,
                    'field_id'   => $slot['fieldId'],
                    'start_time' => $slot['start_time'],
                    'end_time'   => $slot['end_time'],
                    'price'      => $slot['price'],
                ]);
            }

            // Semua proses aman? Commit ke database!
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Booking berhasil dibuat! Mengalihkan ke halaman pembayaran...',
                'booking_id' => $booking->id // Kita lempar ID ini untuk redirect nanti
            ]);

        } catch (\Exception $e) {
            // Jika ada error, batalkan semua inputan di atas
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    public function invoice($id)
    {
        // Tarik data booking beserta relasi detail jam, lapangan, dan venuenya
        $booking = Booking::with(['bookingDetails.field', 'venue'])->findOrFail($id);

        // Pastikan hanya user pemilik booking yang bisa melihat invoice ini
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke invoice ini.');
        }

        return view('user.checkout.invoice', compact('booking'));
    }

    // Tambahkan fungsi ini di dalam CheckoutController.php

    public function pay(Request $request, $id)
    {
        $request->validate([
                'payment_method' => 'required|string'
        ]);

        $booking = Booking::findOrFail($id);

        if ($booking->status === 'pending') {
            $booking->update([
                'status' => 'success',
                'payment_method' => $request->payment_method,
            ]);
        }

        return redirect()
            ->route('checkout.invoice', $booking->id)
            ->with('success', 'Pembayaran simulasi berhasil!');
    }
}