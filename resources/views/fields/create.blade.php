@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8" x-data="{ imagePreview: null }">
    
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('venues.fields.index', $venue->id) }}" class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-400 hover:text-emerald-600 hover:shadow-sm transition-all">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Tambah Lapangan</h2>
                <p class="text-xs text-gray-500 uppercase tracking-widest font-medium">{{ $venue->name }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('fields.store', $venue->id) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
            @csrf

            <div class="flex flex-col md:flex-row gap-10">
                
                <div class="w-full md:w-2/5">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Foto Lapangan</label>
                    
                    <div class="relative group">
                        <div class="aspect-[4/3] bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl overflow-hidden flex flex-col items-center justify-center transition-all group-hover:border-emerald-500 group-hover:bg-emerald-50/30">
                            
                            <template x-if="imagePreview">
                                <img :src="imagePreview" class="w-full h-full object-cover">
                            </template>
                            
                            <template x-if="!imagePreview">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-3">
                                        <i class="fa-solid fa-camera text-emerald-500"></i>
                                    </div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Klik Untuk Upload</p>
                                    <p class="text-[8px] text-gray-300 mt-1">Format JPG, PNG (Maks 2MB)</p>
                                </div>
                            </template>

                            <label for="image" class="absolute inset-0 cursor-pointer"></label>
                            <input type="file" name="image" id="image" class="hidden" accept="image/*" 
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

                    <div class="mt-6">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Tipe Lantai</label>
                        <select name="field_type" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-4 py-3 text-sm font-medium focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                            <option value="Vinyl">Vinyl / Karpet</option>
                            <option value="Interlock">Interlock</option>
                            <option value="Parquet">Kayu (Parquet)</option>
                            <option value="Semen">Semen / Beton</option>
                        </select>
                    </div>
                </div>

                <div class="w-full md:w-3/5 space-y-6">
                    
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Nama Lapangan</label>
                        <input type="text" name="name" placeholder="Contoh: Lapangan A atau Court 1" required
                            class="w-full bg-gray-50 border-gray-100 rounded-2xl px-4 py-3 text-sm font-medium focus:ring-emerald-500 focus:border-emerald-500 transition-all placeholder:text-gray-300">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Harga Reguler</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs">Rp</span>
                                <input type="number" name="price_regular" placeholder="50.000" required
                                    class="w-full bg-gray-50 border-gray-100 rounded-2xl pl-10 pr-4 py-3 text-sm font-bold focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-emerald-500 uppercase tracking-widest mb-3">Harga Peak Time</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-300 font-bold text-xs">Rp</span>
                                <input type="number" name="price_peak" placeholder="80.000" required
                                    class="w-full bg-emerald-50/30 border-emerald-100 rounded-2xl pl-10 pr-4 py-3 text-sm font-bold text-emerald-700 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Deskripsi / Catatan Lapangan</label>
                        <textarea name="description" rows="3" placeholder="Misal: Dekat kantin, pencahayaan paling terang..."
                            class="w-full bg-gray-50 border-gray-100 rounded-2xl px-4 py-3 text-sm font-medium focus:ring-emerald-500 focus:border-emerald-500 transition-all placeholder:text-gray-300"></textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-emerald-100 transition-all active:scale-[0.98] uppercase tracking-widest text-xs">
                            Simpan Lapangan Baru
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-gray-50">
            <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center text-amber-500 text-xs">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <p class="text-[10px] text-gray-500 leading-relaxed"><strong class="text-gray-700 block">Harga Peak Time</strong> Biasanya digunakan untuk jam malam atau akhir pekan.</p>
        </div>
        <div class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-gray-50">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-500 text-xs">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <p class="text-[10px] text-gray-500 leading-relaxed"><strong class="text-gray-700 block">Status Aktif</strong> Lapangan yang baru dibuat akan langsung berstatus aktif.</p>
        </div>
    </div>
</div>
@endsection