@extends('layouts.app')

@section('content')
@php
    // List kota utama untuk validasi pengecekan
    $mainCities = [
        'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Utara', 'Jakarta Pusat',
        'Tangerang', 'Tangerang Selatan', 'Bekasi', 'Depok', 'Bogor',
        'Bandung', 'Surabaya', 'Semarang', 'Yogyakarta', 'Solo',
        'Malang', 'Medan', 'Palembang', 'Makassar', 'Denpasar'
    ];
    // Cek apakah kota saat ini adalah kota kustom (tidak ada di list)
    $isCustomCity = !in_array($venue->city, $mainCities);
@endphp

<div class="max-w-5xl mx-auto px-4 py-8" x-data="{ 
    imagePreview: '{{ asset('storage/' . $venue->image) }}',
    isOther: {{ $isCustomCity ? 'true' : 'false' }} 
}">
    
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('venues.index') }}" class="inline-flex items-center text-emerald-700 hover:text-emerald-800 font-bold transition-colors mb-2">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Kembali ke Daftar Gedung
            </a>
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Edit Gedung: {{ $venue->name }}</h2>
            <p class="text-sm text-gray-500">Perbarui informasi dasar dan identitas visual gedung Anda.</p>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('venues.update', $venue->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="relative w-full h-80 bg-gray-100 group overflow-hidden border-b border-gray-100">
                <template x-if="imagePreview">
                    <img :src="imagePreview" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                </template>
                <div x-show="!imagePreview" class="flex flex-col items-center justify-center h-full text-gray-400">
                    <i class="fa-solid fa-image text-5xl mb-2"></i>
                    <span>Belum ada foto utama</span>
                </div>

                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center backdrop-blur-[2px]">
                    <label for="image_upload" class="cursor-pointer flex flex-col items-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-xl mb-3 hover:scale-110 transition-transform">
                            <i class="fa-solid fa-camera text-emerald-600 text-xl"></i>
                        </div>
                        <span class="text-white font-bold text-sm">Ganti Foto Utama</span>
                        <span class="text-white/70 text-[10px] uppercase tracking-widest mt-1">Maks. 2MB (JPG, PNG, WEBP)</span>
                    </label>
                    <input id="image_upload" type="file" name="image" class="hidden" accept="image/*"
                           @change="
                               const file = $event.target.files[0];
                               if (file) {
                                   const reader = new FileReader();
                                   reader.onload = (e) => { imagePreview = e.target.result; };
                                   reader.readAsDataURL(file);
                               }
                           ">
                </div>
            </div>

            <div class="p-8 lg:p-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700 ml-1">Nama Gedung</label>
                        <input name="name" type="text" placeholder="Masukkan nama gedung" 
                               class="w-full px-5 py-4 rounded-2xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-all text-gray-700" 
                               value="{{ old('name', $venue->name) }}" required />
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700 ml-1">Kategori Utama</label>
                        <div class="relative">
                            <select name="category" class="w-full px-5 py-4 rounded-2xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-all appearance-none bg-white text-gray-700 font-medium">
                                <option value="Badminton" {{ old('category', $venue->category) == 'Badminton' ? 'selected' : '' }}>Badminton</option>
                                <option value="Padel" {{ old('category', $venue->category) == 'Padel' ? 'selected' : '' }}>Padel</option>
                            </select>
                            <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700 ml-1">Kota</label>
                        <div class="relative">
                            <select 
                                name="city_select" 
                                @change="isOther = ($event.target.value === 'other')"
                                class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none appearance-none bg-white text-gray-700"
                                :name="isOther ? '' : 'city'"
                                :required="!isOther">
                                <option value="" disabled>Pilih Kota</option>
                                @foreach($mainCities as $cityName)
                                    <option value="{{ $cityName }}" {{ $venue->city == $cityName ? 'selected' : '' }}>
                                        {{ $cityName }}
                                    </option>
                                @endforeach
                                <option value="other" class="font-bold text-emerald-700" {{ $isCustomCity ? 'selected' : '' }}>
                                    + Lainnya (Ketik Manual)
                                </option>
                            </select>
                            <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-location-dot text-xs"></i>
                            </div>
                        </div>

                        <div x-show="isOther" 
                             x-transition 
                             class="mt-3 p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                            <label class="text-[10px] font-bold text-emerald-700 uppercase mb-1 block">Masukkan Nama Kota</label>
                            <input 
                                type="text" 
                                name="city_manual" 
                                class="w-full px-4 py-3 rounded-xl border border-emerald-200 focus:ring-2 focus:ring-emerald-500/20 outline-none text-sm"
                                placeholder="Misal: Magelang"
                                value="{{ $isCustomCity ? $venue->city : '' }}"
                                :name="isOther ? 'city' : ''"
                                :required="isOther">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700 ml-1">Nomor HP/WhatsApp</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-gray-400 font-medium">
                                +62
                            </div>
                            <input name="phone_number" type="text" placeholder="812xxxxxx"
                                   class="w-full pl-14 pr-5 py-4 rounded-2xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-all text-gray-700" 
                                   value="{{ old('phone_number', $venue->phone_number) }}" required />
                        </div>
                    </div>
                </div>

                <div class="mt-8 space-y-2">
                    <label class="block text-sm font-bold text-gray-700 ml-1">Alamat Lengkap</label>
                    <textarea name="address" rows="4" placeholder="Tulis alamat detail di sini..."
                              class="w-full px-5 py-4 rounded-2xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-all text-gray-700">{{ old('address', $venue->address) }}</textarea>
                </div>

                <div class="flex justify-end pt-10 border-t border-gray-50 mt-10">
                    <button type="submit" class="w-full md:w-auto bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-12 py-4 rounded-2xl shadow-xl shadow-emerald-100 transition-all active:scale-95 flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan Data</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-start space-x-4">
            <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-image text-amber-600"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 text-sm">Visual Utama</h4>
                <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">Gunakan foto sisi depan gedung yang terang agar pelanggan mengenali GOR Anda dengan mudah.</p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-start space-x-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-map-location-dot text-emerald-600"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 text-sm">Alamat Akurat</h4>
                <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">Pastikan alamat sesuai dengan Google Maps untuk menghindari pembatalan dari pelanggan.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-start space-x-4">
            <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-gear text-purple-600"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 text-sm">Pengaturan Jam</h4>
                <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">Untuk mengatur jam buka dan harga peak, silakan akses melalui menu <b>Pengaturan</b> di sidebar.</p>
            </div>
        </div>
    </div>
</div>
@endsection