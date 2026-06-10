<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
{
    $ownerId = Auth::id();

    // 💰 POIN 1: Menghitung Total Pendapatan Bulan Ini (Sudah Berhasil)
    $totalPendapatanBulanIni = Booking::whereHas('venue', function ($query) use ($ownerId) {
            $query->where('user_id', $ownerId);
        })
        ->where('status', 'success')
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->sum('net_profit_owner');

    // 🏸 POIN 2: Menghitung Jumlah Lapangan Terpakai Hari Ini
    // Kita cek data booking yang statusnya sukses dan jadwal mainnya adalah hari ini
    $lapanganTerpakaiHariIni = Booking::whereHas('venue', function ($query) use ($ownerId) {
            $query->where('user_id', $ownerId);
        })
        ->where('status', 'success')
        // Sesuaikan 'booking_date' dengan nama kolom tanggal main di database-mu
        ->whereDate('booking_date', Carbon::today()) 
        ->count(); // Menggunakan count() karena kita mau tahu jumlah lapangannya, bukan uangnya

    // Kirim kedua variabel ke view dashboard
        return view('dashboard', compact('totalPendapatanBulanIni', 'lapanganTerpakaiHariIni'));
    }
}