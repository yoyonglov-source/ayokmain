<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field; // <--- WAJIB TAMBAHKAN INI
use App\Models\FieldBreak;
use Illuminate\Http\Request;
use Carbon\Carbon; // <--- WAJIB TAMBAHKAN INI UNTUK HITUNG WAKTU

class FieldBreakController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'field_id'   => 'required|exists:fields,id',
            'start_time' => 'required',
            'end_time'   => 'required', 
            'date'       => 'nullable|date|after_or_equal:today',
            'reason'     => 'nullable|string|max:255',
        ]);

        $field = Field::findOrFail($request->field_id);
        $field->checkAndResetQuota();

        $startReq = Carbon::parse($request->start_time);
        $endReq = Carbon::parse($request->end_time);
        
        // Proteksi jika end_time melewati tengah malam
        if ($endReq->lt($startReq)) {
            $endReq->addDay();
        }

        // --- LOGIKA PROPORSIONAL JAM OPERASIONAL ---
        
        // Ambil hari (0 = Sunday, 1 = Monday, dst) untuk cari jam operasional
        $targetDate = $request->date ? Carbon::parse($request->date) : now();
        $dayName = $targetDate->format('l'); // Mendapatkan nama hari dalam Inggris (e.g., "Monday")

        $opHour = \App\Models\OperatingHour::where('venue_id', $field->venue_id)
                    ->where('day', $dayName)
                    ->first();

        $minutesToCharge = 0;
        $totalDuration = $startReq->diffInMinutes($endReq);

        if ($opHour && !$opHour->is_closed) {
            $openTime = Carbon::parse($opHour->open_time);
            $closeTime = Carbon::parse($opHour->close_time);
            
            // Sesuaikan tanggal jam operasional dengan tanggal request
            $openTime->setDate($startReq->year, $startReq->month, $startReq->day);
            $closeTime->setDate($startReq->year, $startReq->month, $startReq->day);
            
            // Jika jam tutup melewati tengah malam (misal tutup jam 01:00 pagi)
            if ($closeTime->lt($openTime)) {
                $closeTime->addDay();
            }

            // Hitung irisan (intersection) antara waktu request dan waktu operasional
            $overlapStart = $startReq->gt($openTime) ? $startReq : $openTime;
            $overlapEnd = $endReq->lt($closeTime) ? $endReq : $closeTime;

            if ($overlapStart->lt($overlapEnd)) {
                $minutesToCharge = $overlapStart->diffInMinutes($overlapEnd);
            }
        }

        // --- AKHIR LOGIKA PROPORSIONAL ---

        // 4. KUNCI BISNIS: Cek sisa kuota mingguan (Hanya yang kena charge)
        if ($minutesToCharge > $field->break_quota_minutes) {
            return back()->with('error', "Gagal! Durasi di jam operasional ($minutesToCharge menit) melebihi jatah gratis Anda ({$field->break_quota_minutes} menit).");
        }

        // 5. Simpan Data ke Tabel field_breaks
        FieldBreak::create([
            'field_id'      => $request->field_id,
            'date'          => $request->date,
            'start_time'    => $request->start_time,
            'end_time'      => $request->end_time,
            'reason'        => $request->reason,
            'minutes_saved' => $minutesToCharge,
        ]);

        // 6. POTONG KUOTA (Cukup satu blok saja agar tidak double decrement)
        if ($minutesToCharge > 0) {
            $field->decrement('break_quota_minutes', $minutesToCharge);
            $msg = "Berhasil! Kuota terpotong: $minutesToCharge menit (di jam operasional).";
        } else {
            $msg = "Berhasil! Tidak memotong kuota karena di luar jam operasional.";
        }

            return back()->with('success', $msg);
        }

    public function destroy($id)
    {
        $break = FieldBreak::findOrFail($id);
        $field = Field::find($break->field_id);

        // Ambil angka yang dulu benar-benar dipotong saat simpan
        $refundAmount = $break->minutes_saved; 

        if ($field && $refundAmount > 0) {
            $field->increment('break_quota_minutes', $refundAmount);
            
            if ($field->break_quota_minutes > 120) {
                $field->update(['break_quota_minutes' => 120]);
            }
        }

        $break->delete();

        return back()->with('success', "Jadwal dibuka kembali. Saldo dikembalikan: $refundAmount menit.");
    }
}