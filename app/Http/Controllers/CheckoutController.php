<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Field;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'booking_date' => 'required|date',
            'slots'        => 'required|array|min:1',
            'user_phone'   => 'required|string',
        ]);

        $phone = $request->user_phone;
        $name  = $request->user_name;
        $email = $request->user_email;

        DB::beginTransaction();

        try {

            // =========================
            // AUTO CREATE / FIND USER
            // =========================
            if ($request->is_new_user == true) {

                // Pastikan email & nama diisi kalau user baru
                if (!$name || !$email) {
                    throw new \Exception("Nama dan Email wajib diisi untuk pengguna baru.");
                }

                $user = User::where('phone', $phone)
                    ->orWhere('email', $email)
                    ->first();

                if (!$user) {
                    $user = User::create([
                        'name'     => $name,
                        'email'    => $email,
                        'phone'    => $phone,
                        'is_admin' => false,
                        'password' => bcrypt(Str::random(16)), // Mengisi password string acak agar lolos NOT NULL database
                    ]);
                }

            } else {
                $user = User::where('phone', $phone)->first();
            }

            if (!$user) {
                throw new \Exception("Data pengguna tidak ditemukan di sistem.");
            }

            // Auto Login aman tanpa memicu global redirect di level controller
            Auth::login($user);

            // =========================
            // AMBIL DATA VENUE
            // =========================
            $firstSlot = $request->slots[0];
            $field = Field::findOrFail($firstSlot['fieldId']);
            $venueId = $field->venue_id;

            // =========================
            // HITUNG TOTAL
            // =========================
            $basePrice = 0;
            foreach ($request->slots as $slot) {
                $basePrice += intval($slot['price']);
            }

            $appFee = 2000;
            $pgFee = 3000;
            $totalAmount = $basePrice + $appFee + $pgFee;

            // =========================
            // CREATE BOOKING
            // =========================
            $booking = Booking::create([
                'user_id'          => $user->id,
                'venue_id'         => $venueId,
                'booking_date'     => $request->booking_date,
                'base_price'       => $basePrice,
                'app_fee'          => $appFee,
                'app_fee_bearer'   => 'user',
                'pg_fee'           => $pgFee,
                'pg_fee_bearer'    => 'user',
                'payment_method'   => 'PENDING_SIMULATION',
                'total_amount'     => $totalAmount,
                'net_profit_owner' => $basePrice,
                'status'           => 'pending',
            ]);

            // =========================
            // CREATE BOOKING DETAILS
            // =========================
            foreach ($request->slots as $slot) {
                BookingDetail::create([
                    'booking_id' => $booking->id,
                    'field_id'   => $slot['fieldId'],
                    'start_time' => $slot['start_time'],
                    'end_time'   => $slot['end_time'],
                    'price'      => $slot['price'],
                ]);
            }

            DB::commit();

            // Kembalikan respons murni JSON tanpa instruksi redirect header dari server
            return response()->json([
                'status'     => 'success',
                'message'    => 'Booking berhasil dibuat!',
                'booking_id' => $booking->id
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    public function invoice($id)
    {
        // 1. Ambil data booking lengkap dengan relasi user, detail, lapangan, dan venue
        $booking = Booking::with([
            'user', // Ambil data pemesan untuk ditampilkan di invoice
            'bookingDetails.field',
            'venue'
        ])->findOrFail($id);

        // 2. Validasi keamanan agar user tidak bisa mengintip invoice orang lain
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        // --- LOGIKA HITUNG SISA WAKTU REAL-TIME ---
        $batasWaktuMenit = 20; // Set durasi batas bayar (misal 20 menit)
        $waktuDibuat = \Carbon\Carbon::parse($booking->created_at)->timezone('Asia/Jakarta');
        $waktuHangus = $waktuDibuat->copy()->addMinutes($batasWaktuMenit);
        
        // Hitung selisih detik antara waktu sekarang dengan waktu hangus
        $waktuSekarang = \Carbon\Carbon::now('Asia/Jakarta');
        $sisaDetik = $waktuSekarang->diffInSeconds($waktuHangus, false);
        $sisaDetik = ($sisaDetik < 0) ? 0 : floor($sisaDetik);
        // Jika waktu sudah minus (lewat batas), set jadi 0
        if ($sisaDetik < 0) {
            $sisaDetik = 0;
        }

        // 3. Lempar data ke halaman view blade
        return view('user.checkout.invoice', compact('booking','sisaDetik'));
    }

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
            ->with('success', 'Pembayaran berhasil!');
    }

    // ====================================
    // FONNTE WHATSAPP
    // ====================================

    private function sendWhatsApp($target, $message)
    {
        $token = env('FONNTE_TOKEN');

        $response = Http::withHeaders([
            'Authorization' => $token
        ])->post('https://api.fonnte.com/send', [
            'target' => $target,
            'message' => $message,
            'countryCode' => '62',
        ]);

        return $response->json();
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        // Normalisasi nomor
        $phone = $request->phone;

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);

        } elseif (str_starts_with($phone, '+62')) {
            $phone = substr($phone, 1);
        }

        // Generate OTP
        $otp = rand(1000, 9999);

        // Simpan session
        session([
            'otp_code'       => $otp,
            'otp_phone'      => $phone,
            'otp_expires_at' => now()->addMinutes(15)
        ]);

        $message = "*[AyokMain]* KODE VERIFIKASI ANDA: *{$otp}*\n\nJangan bagikan kode ini kepada siapa pun.";

        $fonnteResponse = $this->sendWhatsApp($phone, $message);

        Log::info('Hasil Respon Fonnte', [
            'response' => $fonnteResponse
        ]);

        if (isset($fonnteResponse['status']) && $fonnteResponse['status'] == true) {

            return response()->json([
                'status'  => 'success',
                'message' => 'OTP berhasil dikirim!',
                'phone'   => $phone
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal mengirim OTP.',
            'debug'   => $fonnteResponse
        ], 500);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp'   => 'required|string|size:4'
        ]);

        $phone = $request->phone;

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);

        } elseif (str_starts_with($phone, '+62')) {
            $phone = substr($phone, 1);
        }

        $sessionOtp   = session('otp_code');
        $sessionPhone = session('otp_phone');
        $expiresAt    = session('otp_expires_at');

        // OTP expired
        if (
            !$sessionOtp ||
            !$expiresAt ||
            now()->greaterThan($expiresAt)
        ) {
            return response()->json([
                'status'  => 'error',
                'message' => 'OTP kedaluwarsa.'
            ], 422);
        }

        // OTP salah
        if (
            $phone !== $sessionPhone ||
            $request->otp != $sessionOtp
        ) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kode OTP salah.'
            ], 422);
        }

        // Hapus session OTP
        session()->forget([
            'otp_code',
            'otp_phone',
            'otp_expires_at'
        ]);

        // Cari user
        $user = User::where('phone', $phone)->first();

        if ($user) {

            return response()->json([
                'status' => 'success',
                'is_new_user' => false,
                'message' => 'Selamat datang kembali.',
                'data' => [
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $phone
                ]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'is_new_user' => true,
            'message' => 'Silakan lengkapi data diri.',
            'data' => [
                'phone' => $phone
            ]
        ]);
    }
}