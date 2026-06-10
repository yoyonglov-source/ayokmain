<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = Auth::id();

        // 📅 Atur default filter tanggal (jika kosong, otomatis ambil sebulan terakhir)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 🔍 Query dasar untuk mengambil booking sukses milik owner ini
        $query = Booking::whereHas('venue', function ($q) use ($ownerId) {
                $q->where('user_id', $ownerId);
            })
            ->where('status', 'success')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        // 💰 Hitung Ringkasan Total dari data yang difilter
        $totalPendapatanKotor = (clone $query)->sum('total_amount');
        $totalPendapatanBersih = (clone $query)->sum('net_profit_owner');
        $totalTransaksi = (clone $query)->count();

        // 📋 Ambil list datanya untuk isi tabel laporan
        $reports = $query->orderBy('created_at', 'desc')->get();

        return view('admin.financial.index', compact(
            'reports',
            'startDate',
            'endDate',
            'totalPendapatanKotor',
            'totalPendapatanBersih',
            'totalTransaksi'
        ));
    }
}