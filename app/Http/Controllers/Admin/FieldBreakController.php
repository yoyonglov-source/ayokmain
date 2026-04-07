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
            'end_time'   => 'required|after:start_time', 
            'date'       => 'nullable|date|after_or_equal:today',
            'reason'     => 'nullable|string|max:255',
        ]);

        // 2. Cari Lapangan & Cek/Reset Kuota (Logic di Model Field)
        $field = Field::findOrFail($request->field_id);
        $field->checkAndResetQuota();

        // 3. Hitung Durasi yang Diminta (Menit)
        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        $duration = $start->diffInMinutes($end);

        // 4. KUNCI BISNIS: Cek sisa kuota mingguan
        if ($duration > $field->break_quota_minutes) {
            return back()->with('error', "Gagal! Durasi ($duration menit) melebihi sisa jatah gratis Anda minggu ini ({$field->break_quota_minutes} menit).");
        }

        // 5. Simpan Data ke Tabel field_breaks
        FieldBreak::create($request->all());

        // 6. POTONG KUOTA: Update sisa kuota di tabel fields
        $field->decrement('break_quota_minutes', $duration);

        return back()->with('success', "Jadwal istirahat lapangan berhasil ditambahkan! Kuota terpakai: $duration menit.");
    }

    public function destroy($id)
{
        // 1. Cari data blokir yang mau dihapus
        $break = FieldBreak::findOrFail($id);
        
        // 2. Cari lapangannya untuk proses refund
        $field = Field::find($break->field_id);

        if ($field) {
            // 3. Hitung durasi yang akan dikembalikan
            $start = Carbon::parse($break->start_time);
            $end = Carbon::parse($break->end_time);
            
            // Proteksi jika jam selesai melewati tengah malam
            if ($end->lt($start)) {
                $end->addDay();
            }

            $duration = $start->diffInMinutes($end);

            // 4. PROSES REFUND: Tambahkan kembali jatah menit ke kolom break_quota_minutes
            $field->increment('break_quota_minutes', $duration);
            
            // Opsional: Batasi agar refund tidak membuat kuota lebih dari 120 menit (jika perlu)
            if ($field->break_quota_minutes > 120) {
                $field->update(['break_quota_minutes' => 120]);
            }
        }

        // 5. Hapus data dari tabel field_breaks
        $break->delete();

        return back()->with('success', "Jadwal berhasil dibuka kembali. Jatah $duration menit telah dikembalikan ke Kuota Anda.");
    }
}