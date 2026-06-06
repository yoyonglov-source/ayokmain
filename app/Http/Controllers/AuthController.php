<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function sendOtpEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Ambil data OTP yang gantung di session (hasil generate dari WA sebelumnya)
        $sessionOtp   = session('otp_code');
        $sessionPhone = session('otp_phone');
        $expiresAt    = session('otp_expires_at');

        // Jika seandainya session kosong atau sudah expired, kita buatkan yang baru sekalian
        if (!$sessionOtp || !$expiresAt || now()->greaterThan($expiresAt)) {
            $sessionOtp = rand(1000, 9999);
            $expiresAt  = now()->addMinutes(5);
            
            session([
                'otp_code' => $sessionOtp,
                'otp_expires_at' => $expiresAt
            ]);
        }

        try {
            // Kirim email berupa teks raw/simpel (tidak perlu file Mailable baru agar cepat)
            Mail::raw("Kode OTP verifikasi AyokMain Anda adalah: {$sessionOtp}. Kode ini berlaku selama 5 menit.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject("Kode OTP Darurat AyokMain");
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Kode OTP berhasil dikirim ke email Anda! Silakan cek inbox atau folder spam.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim email: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // 1. Fungsi untuk Verifikasi OTP (Pindahan dari controller lama)
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone'  => 'required|string',
            'otp'    => 'required|string|size:4',
            'action' => 'nullable|string' // Kita tambah validasi action (opsional)
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

        if (!$sessionOtp || !$expiresAt || now()->greaterThan($expiresAt)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode OTP sudah kedaluwarsa atau tidak valid. Silakan minta kode baru.'
            ], 422);
        }

        if ($phone !== $sessionPhone || $request->otp != $sessionOtp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode OTP yang Anda masukkan salah.'
            ], 422);
        }

        // OTP BENAR! Cukup hapus session code agar tidak di-re-use
        session()->forget('otp_code');

        // Cari tahu apakah nomor ini sudah ada di tabel users
        $user = User::where('phone', $phone)->first();

        if ($user) {
            // CASE A: User Lama -> Langsung Loginkan
            Auth::login($user);
            
            // WAJIB UNTUK LARAVEL 11: Regenerate session agar login-nya mengunci rapat di browser!
            $request->session()->regenerate();
            
            // Bersihkan sisa session karena sudah login
            session()->forget(['otp_phone', 'otp_expires_at']);

            return response()->json([
                'status' => 'success',
                'is_new_user' => false,
                'message' => 'Verifikasi berhasil! Selamat datang kembali.',
                'data' => [
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $phone
                ]
            ]);
        }

        // PENJAGA GAWANG KHUSUS HALAMAN LOGIN HISTORY
        // Jika request datang dari proses login biasa dan user TIDAK ditemukan di DB
        if ($request->action === 'login') {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor WhatsApp Anda belum terdaftar di AyokMain. Silakan lakukan booking lapangan terlebih dahulu.'
            ], 422);
        }

        // CASE B: User Baru (Khusus jalur Checkout) -> Perintahkan frontend buka form Nama & Email
        return response()->json([
            'status' => 'success',
            'is_new_user' => true,
            'message' => 'Verifikasi berhasil! Mohon lengkapi data diri Anda.',
            'data' => [
                'phone' => $phone
            ]
        ]);
    }

    // 2. Fungsi Baru untuk Menyimpan User Baru (Register via OTP)
    public function storeUser(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
        ], [
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'phone.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
        ]);

        try {
            // Insert data ke tabel users sebagai Customer Biasa
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'is_admin' => false, // Default: bukan admin gedung
                'password' => bcrypt(Str::random(16)), // Password di-random aman
            ]);

            // Otomatis loginkan user baru tersebut
            Auth::login($user);

            // Bersihkan sisa session otp
            session()->forget(['otp_phone', 'otp_expires_at']);

            return response()->json([
                'status'  => 'success',
                'message' => 'Akun berhasil dibuat! Mengalihkan ke halaman pembayaran...',
                'data'    => [
                    'user_id' => $user->id,
                    'name'    => $user->name
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data ke database: ' . $e->getMessage()
            ], 500);
        }
    }
}