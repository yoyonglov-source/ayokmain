@extends('layouts.app') {{-- Sesuaikan nama master layout --}}

@section('content')
<div class="space-y-6">
    <!-- HEADER -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">

    <!-- BOX FILTER TANGGAL -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('admin.financial.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-sm bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-700 focus:outline-none focus:border-emerald-500">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-sm bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-700 focus:outline-none focus:border-emerald-500">
            </div>
            <div class="shrink-0">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm px-6 py-2.5 rounded-xl transition duration-150 flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-filter text-xs"></i> Filter Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- STATS CARD FINANSIAL -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-gray-400 uppercase">Omzet Kotor</span>
                <h3 class="text-xl font-black text-gray-800">Rp {{ number_format($totalPendapatanKotor, 0, ',', '.') }}</h3>
                <span class="text-[10px] text-gray-400 block">*Termasuk biaya PG & Aplikasi</span>
            </div>
            <div class="p-3 bg-gray-50 rounded-xl text-gray-400"><i class="fa-solid fa-wallet text-xl"></i></div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between border-l-4 border-l-emerald-500">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-emerald-600 uppercase">Pendapatan Bersih Anda</span>
                <h3 class="text-2xl font-black text-emerald-700">Rp {{ number_format($totalPendapatanBersih, 0, ',', '.') }}</h3>
                <span class="text-[10px] text-emerald-500 font-medium block">✓ Real-time Bersih Masuk Kantong</span>
            </div>
            <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600"><i class="fa-solid fa-money-bill-trend-up text-xl"></i></div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-semibold text-gray-400 uppercase">Total Booking Sukses</span>
                <h3 class="text-xl font-black text-gray-800">{{ $totalTransaksi }} Transaksi</h3>
                <span class="text-[10px] text-gray-400 block">Pada rentang tanggal terpilih</span>
            </div>
            <div class="p-3 bg-gray-50 rounded-xl text-gray-400"><i class="fa-solid fa-chart-line text-xl"></i></div>
        </div>
    </div>

    <!-- TABEL UTAMA LAPORAN -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50">
            <h3 class="font-bold text-gray-800 text-sm">Rincian Riwayat Finansial</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-xs font-bold uppercase tracking-wider text-gray-400 bg-gray-50/50">
                        <th class="px-6 py-4">Tanggal Transaksi</th>
                        <th class="px-6 py-4">Kode Booking</th>
                        <th class="px-6 py-4">Metode</th>
                        <th class="px-6 py-4 text-right">Omzet Kotor</th>
                        <th class="px-6 py-4 text-right text-emerald-700 bg-emerald-50/30">Net Profit Owner</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm text-gray-600">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50/30 transition">
                            <td class="px-6 py-4 text-xs font-medium">
                                {{ \Carbon\Carbon::parse($report->created_at)->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-gray-700">
                                #{{ $report->booking_code ?? $report->id }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold bg-gray-100 text-gray-600 px-2 py-1 rounded-md uppercase">
                                    {{ str_replace('_', ' ', $report->payment_method) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-500">
                                Rp {{ number_format($report->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600 bg-emerald-50/20">
                                Rp {{ number_format($report->net_profit_owner, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                Tidak ada data transaksi sukses ditemukan pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endempty
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection