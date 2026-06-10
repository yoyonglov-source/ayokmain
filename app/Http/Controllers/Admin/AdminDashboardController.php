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

    //Mengambil 5 Data Booking Terbaru
    $bookingTerbaru = Booking::whereHas('venue', function ($query) use ($ownerId) {
            $query->where('user_id', $ownerId);
        })
        ->latest() // Mengurutkan dari yang paling baru dibuat
        ->take(5)  // Ambil 5 data saja
        ->get();

    //Mengambil Lapangan beserta Jumlah Booking Suksesnya
    $statistikLapangan = \App\Models\Field::whereHas('venue', function ($query) use ($ownerId) {
        $query->where('user_id', $ownerId);
    })
        ->withCount(['bookings' => function ($query) {
            $query->where('status', 'success'); // Hanya menghitung booking yang sukses/lunas
        }])
        ->get();    

    // Kirim ketiga variabel ke view dashboard
    return view('dashboard', compact('totalPendapatanBulanIni', 'lapanganTerpakaiHariIni', 'bookingTerbaru','statistikLapangan'));
    }
}