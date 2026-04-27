@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.venues.index') }}" class="flex items-center text-emerald-700 font-semibold mb-6 hover:text-emerald-800 transition">
        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Daftar Gedung
    </a>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50">
            <p class="text-sm text-gray-400 mt-1">Lengkapi data gedung Anda untuk mulai mengelola lapangan.</p>
        </div>

        <form action="{{ route('admin.venues.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            <div class="mb-10" x-data="{ imagePreview: null }">
                <label class="text-sm font-bold text-gray-700 block mb-3">Foto Utama Gedung</label>
                
                <div class="relative">
                    <label for="image_upload" 
                        class="group relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-300 rounded-3xl cursor-pointer bg-gray-50 hover:bg-emerald-50/50 hover:border-emerald-500 transition-all duration-300 overflow-hidden">
                        
                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="absolute inset-0 w-full h-full object-cover">
                        </template>

                        <div class="relative z-10 flex flex-col items-center justify-center space-y-3 transition-opacity duration-300"
                            :class="imagePreview ? 'opacity-0 group-hover:opacity-100 bg-black/40 w-full h-full text-white' : 'text-gray-500'">
                            
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center shadow-sm">
                                <i class="fa-solid fa-camera text-xl" :class="imagePreview ? 'text-white' : 'text-emerald-600'"></i>
                            </div>
                            <div class="text-center">
                                <p class="font-bold text-sm" x-text="imagePreview ? 'Ubah Foto Gedung' : 'Upload Foto Gedung'"></p>
                                <p class="text-[10px] opacity-70">Format: JPG, PNG, WEBP (Maks. 10MB)</p>
                            </div>
                        </div>

                        <input id="image_upload" type="file" name="image" class="hidden" accept="image/*"
                            @change="
                                const file = $event.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => { imagePreview = e.target.result; };
                                    reader.readAsDataURL(file);
                                }
                            ">
                    </label>

                    <button type="button" x-show="imagePreview" @click="imagePreview = null; document.getElementById('image_upload').value = ''" 
                        class="absolute -top-2 -right-2 bg-red-500 text-white w-8 h-8 rounded-full shadow-lg hover:bg-red-600 transition flex items-center justify-center z-20">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                @error('image')
                    <div class="flex items-center mt-3 text-red-600 bg-red-50 p-3 rounded-xl border border-red-100">
                        <i class="fa-solid fa-circle-exclamation mr-2 text-sm"></i>
                        <p class="text-xs font-bold">{{ $message }}</p>
                    </div>
                @enderror
                
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Nama Gedung</label>
                    <input type="text" name="name" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none" placeholder="Contoh: GOR Sejahtera" required>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Kategori Utama</label>
                    <select name="category" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none appearance-none">
                        <option value="Badminton">Badminton</option>
                        <option value="Padel">Padel</option>
                        <option value="Futsal">Futsal</option>
                    </select>
                </div>

                <div class="space-y-2" x-data="{ isOther: false }">
                    <label class="text-sm font-bold text-gray-700">Kota</label>
                    
                    <select 
                        name="city_select" 
                        @change="isOther = ($event.target.value === 'other')"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none appearance-none bg-white"
                        :name="isOther ? '' : 'city'"
                        :required="!isOther">
                        <option value="" disabled selected>Pilih Kota</option>
                        <option value="Jakarta Selatan">Jakarta Selatan</option>
                        <option value="Jakarta Barat">Jakarta Barat</option>
                        <option value="Jakarta Timur">Jakarta Timur</option>
                        <option value="Jakarta Utara">Jakarta Utara</option>
                        <option value="Jakarta Pusat">Jakarta Pusat</option>
                        <option value="Tangerang">Tangerang</option>
                        <option value="Tangerang Selatan">Tangerang Selatan</option>
                        <option value="Bekasi">Bekasi</option>
                        <option value="Depok">Depok</option>
                        <option value="Bogor">Bogor</option>
                        <option value="Bandung">Bandung</option>
                        <option value="Surabaya">Surabaya</option>
                        <option value="Semarang">Semarang</option>
                        <option value="Yogyakarta">Yogyakarta</option>
                        <option value="Solo">Solo</option>
                        <option value="Malang">Malang</option>
                        <option value="Medan">Medan</option>
                        <option value="Palembang">Palembang</option>
                        <option value="Makassar">Makassar</option>
                        <option value="Denpasar">Denpasar</option>
                        <option value="other" class="font-bold text-emerald-700">+ Lainnya (Ketik Manual)</option>
                    </select>

                    <div x-show="isOther" 
                        x-transition 
                        class="mt-3 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                        <label class="text-xs font-bold text-emerald-700 uppercase mb-1 block">Masukkan Nama Kota</label>
                        <input 
                            type="text" 
                            name="city_manual" 
                            class="w-full px-4 py-2 rounded-lg border border-emerald-200 focus:ring-2 focus:ring-emerald-500/20 outline-none"
                            placeholder="Misal: Magelang"
                            :name="isOther ? 'city' : ''"
                            :required="isOther">
                        <p class="text-[10px] text-emerald-600/70 mt-2">
                            *Kota yang Anda masukkan akan kami tinjau untuk ditambahkan ke daftar utama.
                        </p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Nomor HP/WhatsApp</label>
                    <input type="text" name="phone_number" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none" placeholder="0812..." required>
                </div>
            </div>

            <div class="mt-8 space-y-2">
                <label class="text-sm font-bold text-gray-700">Alamat Lengkap</label>
                <textarea name="address" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none" placeholder="Tulis alamat detail di sini..." required></textarea>
            </div>

            <div class="mt-10 flex justify-end">
                <button type="submit" class="bg-emerald-700 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-800 shadow-lg shadow-emerald-700/20 transition-all active:scale-95">
                    Simpan Gedung Baru
                </button>
            </div>
        </form>
    </div>

    <x-venue-footer-tips />
</div>
@endsection