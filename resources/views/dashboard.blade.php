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

    </div>

</div>
@endsection