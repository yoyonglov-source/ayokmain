@extends('layouts.user')

@section('content')
<div class="max-w-xl mx-auto py-12 px-4">
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-bold text-gray-800">Konfirmasi Pembayaran</h2>
        <p class="text-gray-500 mt-2">Selesaikan pembayaran untuk mengamankan slot lapangan Anda.</p>
        <p class="text-xs text-gray-400 mt-1 uppercase font-semibold tracking-wider">Order ID: #{{ $booking->id }} &bull; Status: {{ $booking->status }}</p>
    </div>

    @if($booking->status === 'pending')
    <div 
        x-data="{ 
            countdown: 0,

            get timeDisplay() {

                if (this.countdown <= 0) {
                    return '00:00';
                }

                let menit = Math.floor(this.countdown / 60);
                let detik = this.countdown % 60;

                return `${String(menit).padStart(2, '0')}:${String(detik).padStart(2, '0')}`;
            },

            init() {

                // ambil dari attribute data-sisa
                this.countdown = parseInt(this.$el.dataset.sisa);

                if (this.countdown <= 0) {
                    this.handleHangus();
                    return;
                }

                let timer = setInterval(() => {

                    if (this.countdown > 0) {

                        this.countdown--;

                    } else {

                        clearInterval(timer);
                        this.handleHangus();

                    }

                }, 1000);
            },

            handleHangus() {
                alert('Waktu pembayaran telah habis!');
                window.location.href = '/';
            }
        }"

        data-sisa="{{ $sisaDetik }}"

        class="mb-6 p-4 bg-amber-50 rounded-2xl border border-amber-200 flex items-center justify-between shadow-sm"
    >

        <!-- kiri -->
        <div class="flex items-center space-x-3">

            <div class="p-2 bg-amber-500 text-white rounded-xl animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <div>
                <h4 class="text-sm font-bold text-amber-800">
                    Selesaikan Sisa Pembayaran
                </h4>

                <p class="text-xs text-amber-600">
                    Segera bayar sebelum slot jam lapangan Anda hangus.
                </p>
            </div>
        </div>

        <!-- kanan -->
        <div class="text-right">

            <span
                class="text-2xl font-black text-amber-600 font-mono"
                x-text="timeDisplay">
                00:00
            </span>

        </div>

    </div>
    @endif

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
                    {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d M Y') }}
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-layer-group"></i>
                    {{ $booking->bookingDetails->count() }} Sesi Terpilih
                </div>
            </div>
        </div>

        <div class="p-8 pb-0">
            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Detail Pemesan</h3>
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-gray-700">
                        <span class="text-gray-400 font-normal">Nama:</span> {{ $booking->user->name }}
                    </p>
                    <p class="text-sm font-semibold text-gray-700">
                        <span class="text-gray-400 font-normal">No. WA:</span> +{{ $booking->user->phone }}
                    </p>
                </div>
            </div>
        </div>

        <div class="p-8 space-y-6">
            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-gray-400 mb-3">Item Lapangan</h4>
                <div class="divide-y divide-gray-100 border border-gray-50 rounded-2xl p-4 bg-gray-50/50">
                    @foreach($booking->bookingDetails as $detail)
                    <div class="py-2.5 flex justify-between items-center text-sm first:pt-0 last:pb-0">
                        <div>
                            <p class="font-bold text-gray-700">{{ $detail->field->name }}</p>
                            <p class="text-xs text-gray-400">{{ date('H:i', strtotime($detail->start_time)) }} - {{ date('H:i', strtotime($detail->end_time)) }} WIB</p>
                        </div>
                        <span class="font-semibold text-gray-600">Rp {{ number_format($detail->price) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-gray-400 mb-3">Rincian Biaya</h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Sewa Lapangan</span>
                        <span class="font-bold text-gray-800">Rp {{ number_format($booking->base_price) }}</span>
                    </div>

                    @if($booking->app_fee_bearer === 'user')
                    <div class="flex justify-between text-gray-600">
                        <span>Biaya Layanan</span>
                        <span class="font-bold text-gray-800">Rp {{ number_format($booking->app_fee) }}</span>
                    </div>
                    @endif

                    @if($booking->pg_fee_bearer === 'user')
                    <div class="flex justify-between text-gray-600">
                        <span>Biaya Administrasi</span>
                        <span class="font-bold text-gray-800">Rp {{ number_format($booking->pg_fee) }}</span>
                    </div>
                    @endif

                    <div class="pt-5 border-t border-dashed border-gray-200 mt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-800">Total Pembayaran</span>
                            <span class="text-2xl font-black text-emerald-600">Rp {{ number_format($booking->total_amount) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-gray-400 mb-3">Pilih Metode Simulasi</h4>
                
                <form action="{{ route('checkout.pay', $booking->id) }}" method="POST" class="space-y-3">
                    @csrf
                    
                    <label class="flex items-center gap-3 p-3.5 border border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50 transition bg-white block">
                        <input type="radio" name="payment_method" value="QRIS" checked class="text-emerald-600 focus:ring-emerald-500">
                        <div class="text-sm">
                            <p class="font-bold text-gray-700">QRIS (DANA, OVO, LinkAja)</p>
                            <p class="text-xs text-gray-400">Scan otomatis barcode via handphone</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 border border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50 transition bg-white block">
                        <input type="radio" name="payment_method" value="MANDIRI_VA" class="text-emerald-600 focus:ring-red-500">
                        <div class="text-sm">
                            <p class="font-bold text-gray-700">Mandiri Virtual Account</p>
                            <p class="text-xs text-gray-400">Transfer m-Banking otomatis</p>
                        </div>
                    </label>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-5 rounded-2xl mt-6 shadow-lg shadow-emerald-100 transition-all active:scale-95 uppercase tracking-widest text-sm">
                        Simulasikan Bayar Sekarang
                    </button>
                </form>
            </div>
            
            <p class="text-center text-xs text-gray-400 mt-4">
                <i class="fa-solid fa-shield-halved mr-1"></i> Pembayaran aman & terverifikasi otomatis
            </p>
        </div>
    </div>
</div>
@endsection