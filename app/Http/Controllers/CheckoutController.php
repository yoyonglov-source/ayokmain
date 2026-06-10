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
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketBookingPaid;

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
            // =======================================================
            // GERBANG 1: VALIDASI TABRAKAN SEBELUM INVOICE DIBUAT
            // =======================================================
            foreach ($request->slots as $slot) {
                // Cari tahu apakah slot jam tersebut sudah dikunci oleh user lain
                $slotSudahTerisi = BookingDetail::where('field_id', $slot['fieldId'])
                    ->where('start_time', $slot['start_time'])
                    ->whereHas('booking', function ($query) use ($request) {
                        $query->where('booking_date', $request->booking_date)
                              ->whereIn('status', ['success', 'pending']); // pending dan success dianggap mengunci
                    })
                    ->exists();

                if ($slotSudahTerisi) {
                    // Beri tahu user pertama secara halus
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Waduh! Slot lapangan pada jam ' . date('H:i', strtotime($slot['start_time'])) . ' baru saja dipesan oleh orang lain beberapa detik yang lalu. Silakan pilih slot jam atau hari lain.'
                    ], 422);
                }
            }

            // =========================
            // AUTO CREATE / FIND USER
            // =========================
            if ($request->is_new_user == true) {
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
                        'password' => bcrypt(Str::random(16)),
                    ]);
                }
            } else {
                $user = User::where('phone', $phone)->first();
            }

            if (!$user) {
                throw new \Exception("Data pengguna tidak ditemukan di sistem.");
            }

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
                'field_id'         => $field->id,
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
        $booking = Booking::with([
            'user',
            'bookingDetails.field',
            'venue'
        ])->findOrFail($id);

        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $batasWaktuMenit = 20;
        $waktuDibuat = Carbon::parse($booking->created_at)->timezone('Asia/Jakarta');
        $waktuHangus = $waktuDibuat->copy()->addMinutes($batasWaktuMenit);
        
        $waktuSekarang = Carbon::now('Asia/Jakarta');
        $sisaDetik = $waktuSekarang->diffInSeconds($waktuHangus, false);
        $sisaDetik = ($sisaDetik < 0) ? 0 : floor($sisaDetik);

        return view('user.checkout.invoice', compact('booking', 'sisaDetik'));
    }

    public function pay(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string'
        ]);

        // 1. Mulai transaksi database
        DB::beginTransaction();

        try {
            // 2. Ambil data booking dengan mengunci barisnya di DB agar tidak bisa diganggu request lain saat ini
            $booking = Booking::with('bookingDetails')->lockForUpdate()->findOrFail($id);

            if ($booking->status !== 'pending') {
                DB::rollBack();
                return redirect()->route('checkout.invoice', $booking->id);
            }

            // 3. Validasi ulang slot lapangan dengan proteksi ketat
            foreach ($booking->bookingDetails as $detail) {
                $sudahDilunasiOrangLain = BookingDetail::where('field_id', $detail->field_id)
                    ->where('start_time', $detail->start_time)
                    ->whereHas('booking', function ($query) use ($booking) {
                        $query->where('booking_date', $booking->booking_date)
                            ->where('status', 'success')
                            ->where('id', '!=', $booking->id);
                    })
                    ->exists();

                if ($sudahDilunasiOrangLain) {
                    // Batalkan pesanan karena keduluan
                    $booking->update(['status' => 'cancelled']);
                    
                    // Commit perubahan status 'cancelled'
                    DB::commit(); 

                    return redirect()
                        ->route('checkout.invoice', $booking->id)
                        ->with('error', 'Waduh, Maaf! Slot lapangan ini baru saja lunas dibayar oleh pemesan lain.');
                }
            }

            // 4. Update status utama ke success
            $booking->update([
                'status'         => 'success',
                'payment_method' => $request->payment_method,
            ]);

            // =========================================================================
            // TIPS PENTING: Jika ada kirim WA / Logika luar, taruh DI SINI sebelum commit.
            // Jika kirim WA gagal, sistem otomatis rollback, status success dibatalkan!
            // =========================================================================
            // $this->kirimWhatsAppNotifikasi($booking); 

            // 5. Jika semua baris di atas sukses tanpa error, baru sahkan ke database!
            Mail::to($booking->user->email)->send(new TicketBookingPaid($booking)); // kirim tiket via mail + bukti pembayaran lunas
            DB::commit();

            return redirect()
                ->route('checkout.invoice', $booking->id)
                ->with('success', 'Pembayaran berhasil!');

        } catch (\Exception $e) {
            // Jika ada error jaringan, query salah, atau sistem hang di tengah jalan, 
            // BATALKAN SEMUA PERUBAHAN! Status database kembali suci seolah tidak terjadi apa-apa.
            DB::rollBack();
            
            return redirect()
                ->route('checkout.invoice', $id)
                ->with('error', 'Terjadi kesalahan sistem saat memproses pembayaran: ' . $e->getMessage());
        }
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
        'phone' => [
            'required',
            'string',
            'regex:/^([0-9\s\-\+\(\)]*)$/', // Hanya boleh angka, spasi, tanda +, -, atau kurung
            'min:9',                         // Nomor HP minimal 9 digit (misal: 081234567)
            'max:14'                         // Nomor HP maksimal 14 digit
        ]
        ], [
            'phone.required' => 'Nomor WhatsApp wajib diisi!',
            'phone.regex'    => 'Format nomor WhatsApp tidak valid. Tolong masukkan angka saja!',
            'phone.min'      => 'Nomor WhatsApp terlalu pendek!',
            'phone.max'      => 'Nomor WhatsApp terlalu panjang!',
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