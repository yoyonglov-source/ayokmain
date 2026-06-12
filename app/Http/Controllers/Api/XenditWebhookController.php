<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Ambil Xendit Callback Token dari header kiriman Xendit
        $xenditXCallbackToken = $request->header('x-callback-token');
        
        // Ambil token rahasia yang kita simpan di file .env milik kita nanti
        $localCallbackToken = env('XENDIT_CALLBACK_TOKEN');

        // 🛡️ SECURITY CHECK: Pastikan token dari Xendit cocok dengan .env kita
        if ($xenditXCallbackToken !== $localCallbackToken) {
            Log::warning('⚠️ Xendit Webhook: Upaya akses ilegal ditolak! Token tidak cocok.');
            return response()->json(['message' => 'Invalid callback token'], 401);
        }

        // 📦 Ambil payload data JSON dari Xendit
        $payload = $request->all();
        
        // Simpan log biar kita bisa pantau struktur datanya saat testing nanti
        Log::info('📩 Xendit Webhook Terdeteksi:', $payload);

        // 🔑 Ambil UUID Booking yang dikirim oleh Xendit via external_id
        $bookingCode = $payload['external_id'] ?? ($payload['id'] ?? null);
        $status = $payload['status'] ?? null;

        if (!$bookingCode) {
            return response()->json(['message' => 'External ID not found'], 400);
        }

        // 🔍 REVISI DI SINI: Cari data booking berdasarkan kolom UUID, bukan id internal lagi
        $booking = Booking::where('uuid', $bookingCode)->first();

        if (!$booking) {
            Log::error("❌ Xendit Webhook: Data Booking UUID #{$bookingCode} tidak ditemukan di database.");
            return response()->json(['message' => 'Booking not found'], 404);
        }

        // 🚦 JIKA STATUSNYA SUKSES / SETTLED / PAID
        if (in_array($status, ['PAID', 'SETTLED', 'SUCCESSFUL'])) {
            
            // Cek dulu, kalau statusnya sudah sukses, jangan diupdate lagi (biar tidak double proses)
            if ($booking->status !== 'success') {
                $booking->update([
                    'status' => 'success',
                ]);

                Log::info("✅ Xendit Webhook: Booking UUID #{$bookingCode} BERHASIL DILUNASI. Status otomatis diubah ke SUCCESS.");
            }
            
        } elseif (in_array($status, ['EXPIRED', 'FAILED'])) {
            
            // Jika pembayaran kadaluarsa atau gagal, ubah status ke cancelled
            $booking->update(['status' => 'cancelled']);
            Log::info("❌ Xendit Webhook: Booking UUID #{$bookingCode} GAGAL/EXPIRED.");
            
        }

        // 🤝 Kirim balik respon 200 OK ke Xendit
        return response()->json(['message' => 'Callback handled successfully'], 200);
    }
}