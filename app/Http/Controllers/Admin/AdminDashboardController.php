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
        ->take(7)  // Ambil 7 data saja
        ->get();

    //Mengambil Lapangan beserta Jumlah Booking Suksesnya
    $statistikLapangan = \App\Models\Field::whereHas('venue', function ($query) use ($ownerId) {
        $query->where('user_id', $ownerId);
    })
        ->withCount(['bookings' => function ($query) {
            $query->where('status', 'success'); // Hanya menghitung booking yang sukses/lunas
        }])
        ->get();    

    //Mengambil 7 Data Booking Terbaru
    $bookingTerbaru = Booking::whereHas('venue', function ($query) use ($ownerId) {
            $query->where('user_id', $ownerId);
        })
        ->latest()
        ->take(5)
        ->get();

    // ⚡ OPSI 1: RINGKASAN SLOT HARI INI
    // Ambil lapangan beserta list data jam booking khusus HARI INI
    $lapanganHariIni = \App\Models\Field::whereHas('venue', function ($query) use ($ownerId) {
            $query->where('user_id', $ownerId);
        })
        ->with(['bookings' => function ($query) {
            $query->where('status', 'success')
                  ->whereDate('booking_date', Carbon::today());
        }])
        ->get();

    // Definisikan list jam operasional GOR kamu (Bisa disesuaikan nanti)
    $listJam = ['07:00','08:00', '09:00', '10:00', '11:00', '12:00', '16:00', '17:00', '19:00', '20:00','21:00','22:00','23:00'];    

    //Menghitung Metode Pembayaran Paling Populer Bulan Ini
    $metodePembayaran = Booking::whereHas('venue', function ($query) use ($ownerId) {
        $query->where('user_id', $ownerId);
    })
    ->where('status', 'success')
    ->whereMonth('created_at', \Carbon\Carbon::now()->month)
    ->whereYear('created_at', \Carbon\Carbon::now()->year)
    ->select('payment_method', \DB::raw('count(*) as total_transaksi'))
    ->groupBy('payment_method')
    ->orderBy('total_transaksi', 'desc')
    ->get();

    // Kirim ketiga variabel ke view dashboard
    return view('dashboard', compact('totalPendapatanBulanIni', 'lapanganTerpakaiHariIni', 'bookingTerbaru','statistikLapangan','lapanganHariIni','listJam','metodePembayaran'));
    }
}