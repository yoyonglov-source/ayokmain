@extends('layouts.user')

@section('content')
<div class="bg-[#0d8173] py-6 shadow-sm">
    <div class="max-w-4xl mx-auto px-6">
        <h1 class="text-xl font-bold text-white tracking-wide uppercase">Bookingan Saya</h1>
        <p class="text-xs text-white/80 mt-1">Pantau status pembayaran dan e-ticket lapangan Anda di sini.</p>
    </div>
</div>

<main class="max-w-4xl mx-auto py-8 px-6">
    <div class="space-y-4">
        @forelse($bookings as $booking)
            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-gray-400">#AM-{{ $booking->id }}</span>
                        
                        @if($booking->status === 'success')
                            <span class="bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full border border-emerald-200">Lunas / Sukses</span>
                        @elseif($booking->status === 'pending')
                            <span class="bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full border border-amber-200">Menunggu Bayar</span>
                        @else
                            <span class="bg-gray-50 text-gray-500 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full border border-gray-200">Dibatalkan</span>
                        @endif
                    </div>

                    <h3 class="text-lg font-extrabold text-gray-800">{{ $booking->venue->name }}</h3>
                    
                    <div class="text-xs text-gray-500 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-gray-400"></i>
                            <span>{{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('l, d F Y') }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-regular fa-clock text-gray-400"></i>
                            <span>
                                @foreach($booking->bookingDetails as $detail)
                                    {{ $detail->field->name ?? 'Lapangan' }} ({{ date('H:i', strtotime($detail->start_time)) }}-{{ date('H:i', strtotime($detail->end_time)) }} WIB){{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-center gap-2 pt-4 md:pt-0 border-t md:border-t-0 border-gray-100">
                    <div>
                        <div class="text-[10px] text-gray-400 uppercase font-bold text-left md:text-right">Total Bayar</div>
                        <div class="text-base font-black text-gray-700">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</div>
                    </div>

                    @if($booking->status === 'success')
                        <a href="{{ url('/checkout/invoice/'.$booking->id) }}" class="bg-[#0d8173] hover:bg-[#0a665b] text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center gap-1">
                            <i class="fa-solid fa-ticket text-[10px]"></i> Lihat Tiket
                        </a>
                    @elseif($booking->status === 'pending')
                        <a href="{{ url('/checkout/invoice/'.$booking->id) }}" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center gap-1">
                            Bayar Sekarang
                        </a>
                    @else
                        <button disabled class="bg-gray-100 text-gray-400 text-xs font-bold px-4 py-2.5 rounded-xl cursor-not-allowed">
                            Expired
                        </button>
                    @endif
                </div>

            </div>
        @empty
            <div class="bg-gray-50 border border-dashed border-gray-200 rounded-2xl p-12 text-center">
                <div class="text-gray-300 text-4xl mb-3">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-600">Belum Ada Riwayat Booking</h3>
                <p class="text-xs text-gray-400 mt-1">Lapangan yang Anda sewa nanti akan muncul daftarnya di sini.</p>
                <a href="{{ url('/') }}" class="inline-block mt-4 bg-[#0d8173] text-white text-xs font-bold px-4 py-2 rounded-xl">Cari Lapangan</a>
            </div>
        @endforelse
    </div>
</main>
@endsection