@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.booking.history', ['status' => 'all', 'search' => $search]) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ ($status == 'all' || !$status) ? 'bg-gray-800 text-white' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                Semua
            </a>
            <a href="{{ route('admin.booking.history', ['status' => 'success', 'search' => $search]) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $status == 'success' ? 'bg-emerald-600 text-white' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                Sukses
            </a>
            <a href="{{ route('admin.booking.history', ['status' => 'pending', 'search' => $search]) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $status == 'pending' ? 'bg-amber-500 text-white' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                Pending
            </a>
            <a href="{{ route('admin.booking.history', ['status' => 'cancelled', 'search' => $search]) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $status == 'cancelled' ? 'bg-red-500 text-white' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                Batal
            </a>
        </div>

        <form action="{{ route('admin.booking.history') }}" method="GET" class="w-full md:w-72 flex gap-2 m-0">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari Kode / Nama..." class="w-full text-xs bg-gray-50 border border-gray-200 rounded-xl pl-9 pr-4 py-2.5 text-gray-700 focus:outline-none focus:border-emerald-500">
                <div class="absolute left-3 top-3 text-gray-400 text-xs">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
            <button type="submit" class="bg-gray-100 text-gray-700 text-xs font-bold px-4 py-2.5 rounded-xl hover:bg-gray-200 transition">
                Cari
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-xs font-bold uppercase tracking-wider text-gray-400 bg-gray-50/50">
                        <th class="px-6 py-4">Informasi Transaksi</th>
                        <th class="px-6 py-4">Customer & Kontak</th>
                        <th class="px-6 py-4">Jadwal Main (Sewa)</th>
                        <th class="px-6 py-4">Total Bayar</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm text-gray-600">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50/30 transition">
                            <td class="px-6 py-4">
                                <div class="font-mono font-bold text-gray-800">#{{ $booking->booking_code ?? $booking->id }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">Order: {{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-700">{{ $booking->user->name ?? 'Walk-in / Guest' }}</div>
                                <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $booking->user->phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-bold text-gray-700 mb-1">
                                    <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded-md text-[10px] uppercase mr-1">
                                        {{ $booking->field->name ?? 'Lapangan' }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-600 font-medium flex items-center gap-1">
                                    <i class="fa-regular fa-calendar text-gray-400 text-[11px]"></i>
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}
                                </div>
                                <div class="text-[11px] text-gray-400 font-mono mt-0.5 flex items-center gap-1">
                                    <i class="fa-regular fa-clock text-[10px]"></i> 
                                    
                                    @if($booking->details->first())
                                        {{ \Carbon\Carbon::parse($booking->details->first()->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->details->first()->end_time)->format('H:i') }}
                                    @else
                                        <span class="text-red-400 italic text-[10px]">Jam tidak tercatat</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-800">
                                Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($booking->status == 'success')
                                    <span class="inline-block text-[10px] font-extrabold tracking-wider uppercase bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full border border-emerald-200">Success</span>
                                @elseif($booking->status == 'pending')
                                    <span class="inline-block text-[10px] font-extrabold tracking-wider uppercase bg-amber-50 text-amber-600 px-3 py-1 rounded-full border border-amber-200">Pending</span>
                                @else
                                    <span class="inline-block text-[10px] font-extrabold tracking-wider uppercase bg-red-50 text-red-600 px-3 py-1 rounded-full border border-red-200">Cancelled</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                Riwayat booking tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="p-4 bg-gray-50/50 border-t border-gray-50">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection