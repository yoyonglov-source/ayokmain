@extends('layouts.app')

@section('content')
<div class="space-y-6">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div class="space-y-2">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Total Pendapatan (Bulan Ini)</span>
                
                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">
                    Rp {{ number_format($totalPendapatanBulanIni, 0, ',', '.') }}
                </h3>
                
                <div class="flex items-center text-xs text-emerald-600 font-medium bg-emerald-50 px-2.5 py-0.5 rounded-full w-fit">
                    <i class="fa-solid fa-circle-check text-[10px] mr-1"></i>
                    <span class="text-[10px] tracking-wide">Real-time Bersih</span>
                </div>
            </div>
            
            <div class="p-4 bg-emerald-50 rounded-xl text-emerald-600 shadow-inner">
                <i class="fa-solid fa-wallet text-2xl"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div class="space-y-2">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Jadwal Main (Hari Ini)</span>
                
                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">
                    {{ $lapanganTerpakaiHariIni }} <span class="text-sm font-normal text-gray-500">Sesi Terbooking</span>
                </h3>
                
                <div class="flex items-center text-xs text-indigo-600 font-medium bg-indigo-50 px-2.5 py-0.5 rounded-full w-fit">
                    <i class="fa-solid fa-calendar-day text-[10px] mr-1"></i>
                    <span class="text-[10px] tracking-wide">Tanggal: {{ \Carbon\Carbon::today()->format('d M Y') }}</span>
                </div>
            </div>
            
            <div class="p-4 bg-indigo-50 rounded-xl text-indigo-600 shadow-inner">
                <i class="fa-solid fa-table-tennis-paddle-ball text-2xl"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between min-h-[140px]">
            <div class="flex items-center justify-between border-b border-gray-50 pb-2 mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Popularitas Lapangan</span>
                <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                    <i class="fa-solid fa-chart-simple text-sm"></i>
                </div>
            </div>
            
            <div class="flex items-center gap-4 overflow-x-auto pb-1 scrollbar-thin">
                @forelse($statistikLapangan as $lapangan)
                    <div class="flex-1 min-w-[100px] border-r border-gray-100 last:border-none pr-2">
                        <div class="text-xs font-bold text-gray-700 truncate">
                            {{ $lapangan->name }}
                        </div>
                        <div class="text-lg font-black text-amber-600 tracking-tight mt-0.5">
                            {{ $lapangan->bookings_count }} <span class="text-[10px] font-normal text-gray-400 uppercase">Main</span>
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-gray-400 italic py-2">
                        Belum ada data lapangan tersedia.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="font-bold text-gray-800 text-base">Aktivitas Booking Terbaru</h3>
                <p class="text-xs text-gray-400">5 transaksi terakhir yang masuk ke sistem GOR Anda.</p>
            </div>
            <a href="#" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100/80 px-3 py-1.5 rounded-lg transition duration-150">
                Lihat Semua Masuk ➜
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-xs font-bold uppercase tracking-wider text-gray-400 bg-gray-50/30">
                        <th class="px-6 py-4">Kode Booking</th>
                        <th class="px-6 py-4">Nama Customer</th>
                        <th class="px-6 py-4">Tanggal Main</th>
                        <th class="px-6 py-4">Total Bayar</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm text-gray-600">
                    @forelse($bookingTerbaru as $row)
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-6 py-4 font-mono font-bold text-emerald-700">
                                #{{ $row->booking_code ?? $row->id }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $row->user->name ?? $row->customer_name ?? 'Guest User' }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                {{ \Carbon\Carbon::parse($row->booking_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-700">
                                Rp {{ number_format($row->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($row->status == 'success')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        Sukses
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                                <i class="fa-solid fa-folder-open text-2xl mb-2 block text-gray-300"></i>
                                Belum ada aktivitas transaksi masuk bulan ini.
                            </td>
                        </tr>
                    @endempty
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection