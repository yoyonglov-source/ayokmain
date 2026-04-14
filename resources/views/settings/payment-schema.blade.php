@extends('layouts.app')

@section('content')
@if (session('success'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="max-w-4xl mx-auto mb-6 flex items-center p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl shadow-sm">
        <div class="bg-emerald-500 p-2 rounded-lg mr-3">
            <i class="fa-solid fa-check text-white text-xs"></i>
        </div>
        <div class="text-sm font-bold">
            {{ session('success') }}
        </div>
        <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif
<div class="max-w-4xl mx-auto" x-data="{ 
    price: 50000, 
    appFee: {{ $venue->platform_fee }}, 
    pgFeeVA: 4000, 
    feeMode: '{{ $venue->fee_mode }}',
    pgBearer: '{{ $venue->pg_fee_bearer }}'
}">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Skema Pembayaran</h2>
        <p class="text-gray-500">Atur bagaimana biaya layanan dan biaya transaksi dibebankan.</p>
    </div>

    <form action="{{ route('settings.payment-schema.update') }}" method="POST">
        @csrf
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 grid grid-cols-1 lg:grid-cols-2 gap-12">
                
                <div class="space-y-8">
                    <div class="space-y-5">
                        <label class="block text-sm font-bold text-gray-700 ml-1">Biaya Layanan AyokMain (Rp5.000)</label>
                        <div class="grid grid-cols-1 gap-4">
                            <label class="relative flex items-center p-5 border rounded-2xl cursor-pointer transition-all space-x-4"
                                :class="feeMode === 'addon' ? 'border-emerald-500 bg-emerald-50/30' : 'border-gray-100'">
                                <input type="radio" name="fee_mode" value="addon" x-model="feeMode" :checked="feeMode === 'addon'" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="block font-bold text-sm" :class="feeMode === 'addon' ? 'text-emerald-700' : 'text-gray-600'">Bebankan ke User (Add-on)</span>
                                    <span class="text-xs text-gray-500 mt-0.5 block">Harga sewa akan ditambah biaya aplikasi</span>
                                </div>
                            </label>
                            
                            <label class="relative flex items-center p-5 border rounded-2xl cursor-pointer transition-all space-x-4"
                                :class="feeMode === 'deduct' ? 'border-emerald-500 bg-emerald-50/30' : 'border-gray-100'">
                                <input type="radio" name="fee_mode" value="deduct" x-model="feeMode" :checked="feeMode === 'deduct'" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="block font-bold text-sm" :class="feeMode === 'deduct' ? 'text-emerald-700' : 'text-gray-600'">Potong Pendapatan (Deduct)</span>
                                    <span class="text-xs text-gray-500 mt-0.5 block">Anda menerima hasil bersih setelah dipotong biaya aplikasi</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <label class="block text-sm font-bold text-gray-700 ml-1">Biaya Transaksi (Bank/E-Wallet)</label>
                        <div class="grid grid-cols-1 gap-4">
                            <label class="relative flex items-center p-5 border rounded-2xl cursor-pointer transition-all space-x-4"
                                :class="pgBearer === 'customer' ? 'border-emerald-500 bg-emerald-50/30' : 'border-gray-100'">
                                <input type="radio" name="pg_fee_bearer" value="customer" x-model="pgBearer" :checked="pgBearer === 'customer'" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="block font-bold text-sm" :class="pgBearer === 'customer' ? 'text-emerald-700' : 'text-gray-600'">User yang Bayar</span>
                                    <span class="text-xs text-gray-500 mt-0.5 block">Biaya bank ditanggung oleh penyewa</span>
                                </div>
                            </label>

                            <label class="relative flex items-center p-5 border rounded-2xl cursor-pointer transition-all space-x-4"
                                :class="pgBearer === 'owner' ? 'border-emerald-500 bg-emerald-50/30' : 'border-gray-100'">
                                <input type="radio" name="pg_fee_bearer" value="owner" x-model="pgBearer" :checked="pgBearer === 'owner'" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="block font-bold text-sm" :class="pgBearer === 'owner' ? 'text-emerald-700' : 'text-gray-600'">Gedung yang Bayar</span>
                                    <span class="text-xs text-gray-500 mt-0.5 block">Biaya bank memotong pendapatan Anda secara otomatis</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-[2.5rem] p-10 px-12 border border-gray-100 flex flex-col justify-center">
                    <h4 class="font-bold text-gray-800 mb-8 flex items-center gap-4">
                        <div class="bg-emerald-100 p-2.5 rounded-xl">
                            <i class="fa-solid fa-calculator text-emerald-600"></i>
                        </div>
                        <span class="text-lg">Simulasi Pendapatan</span>
                    </h4>
                    
                    <div class="space-y-6 border-b border-dashed border-gray-200 pb-8 text-sm px-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Harga Sewa (Contoh)</span>
                            <span class="font-bold text-gray-800 text-base">Rp 50.000</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Biaya Layanan</span>
                            <span class="font-bold text-base" :class="feeMode === 'addon' ? 'text-emerald-600' : 'text-red-500'" 
                                x-text="feeMode === 'addon' ? '+ Rp 5.000' : '- Rp 5.000'"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Biaya Bank (Est. VA)</span>
                            <span class="font-bold text-base" :class="pgBearer === 'customer' ? 'text-emerald-600' : 'text-red-500'"
                                x-text="pgBearer === 'customer' ? '+ Rp 4.000' : '- Rp 4.000'"></span>
                        </div>
                    </div>

                    <div class="pt-8 space-y-5">
                        <div class="flex justify-between items-center p-6 bg-white rounded-2xl border border-gray-100 shadow-sm mx-1">
                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest text-left">User Bayar</span>
                            <span class="text-xl font-black text-gray-800 text-right" 
                                x-text="'Rp ' + (price + (feeMode === 'addon' ? appFee : 0) + (pgBearer === 'customer' ? pgFeeVA : 0)).toLocaleString()"></span>
                        </div>
                        <div class="flex justify-between items-center p-7 bg-emerald-600 rounded-[2rem] text-white shadow-xl shadow-emerald-200 mx-1">
                            <span class="text-[11px] font-bold opacity-90 uppercase tracking-widest text-left">Anda Terima</span>
                            <span class="text-2xl font-black text-right" 
                                x-text="'Rp ' + (price - (feeMode === 'deduct' ? appFee : 0) - (pgBearer === 'owner' ? pgFeeVA : 0)).toLocaleString()"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-gray-50 border-t border-gray-100 flex justify-center md:justify-end">
                <button type="submit" class="w-full md:w-auto md:min-w-[300px] bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-12 rounded-2xl transition shadow-lg shadow-emerald-200 uppercase tracking-wider text-sm active:scale-95">
                    Simpan Konfigurasi
                </button>
            </div>
        </div>
    </form>
</div>
@endsection