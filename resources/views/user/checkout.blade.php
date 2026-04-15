@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-12 px-4">
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-bold text-gray-800">Konfirmasi Pembayaran</h2>
        <p class="text-gray-500 mt-2">Selesaikan pembayaran untuk mengamankan slot lapangan Anda.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-8 bg-emerald-600 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-emerald-100 text-sm uppercase tracking-wider font-bold">Gedung Olahraga</p>
                    <h3 class="text-2xl font-black mt-1">{{ $booking->venue->name }}</h3>
                </div>
                <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-sm">
                    <i class="fa-solid fa-calendar-check text-2xl"></i>
                </div>
            </div>
            <div class="mt-6 flex gap-6 text-sm font-medium">
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-calendar"></i>
                    {{ date('d M Y', strtotime($booking->booking_date)) }}
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-clock"></i>
                    {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}
                </div>
            </div>
        </div>

        <div class="p-8">
            <h4 class="font-bold text-gray-800 mb-6">Rincian Biaya</h4>
            <div class="space-y-4">
                <div class="flex justify-between text-gray-600">
                    <span>Sewa Lapangan</span>
                    <span class="font-bold text-gray-800">Rp {{ number_format($booking->base_price) }}</span>
                </div>

                @if($booking->app_fee_bearer === 'customer')
                <div class="flex justify-between text-gray-600">
                    <span>Biaya Layanan</span>
                    <span class="font-bold text-gray-800">Rp {{ number_format($booking->app_fee) }}</span>
                </div>
                @endif

                @if($booking->pg_fee_bearer === 'customer')
                <div class="flex justify-between text-gray-600">
                    <span>Biaya Administrasi</span>
                    <span class="font-bold text-gray-800">Rp {{ number_format($booking->pg_fee) }}</span>
                </div>
                @endif

                <div class="pt-6 border-t border-dashed border-gray-200 mt-6">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">Total Pembayaran</span>
                        <span class="text-2xl font-black text-emerald-600">Rp {{ number_format($booking->total_amount) }}</span>
                    </div>
                </div>
            </div>

            <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-5 rounded-2xl mt-10 shadow-lg shadow-emerald-200 transition-all active:scale-95 uppercase tracking-widest">
                Bayar Sekarang
            </button>
            
            <p class="text-center text-xs text-gray-400 mt-6">
                <i class="fa-solid fa-shield-halved mr-1"></i> Pembayaran aman & terverifikasi otomatis
            </p>
        </div>
    </div>
</div>
@endsection