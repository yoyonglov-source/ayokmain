@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8" x-data="{ imagePreview: '{{ $field->image ? asset('storage/' . $field->image) : null }}' }">
    
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('venues.fields.index', $venue->id) }}" class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-400 hover:text-emerald-600 hover:shadow-sm transition-all">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Edit Lapangan: {{ $field->name }}</h2>
                <p class="text-xs text-gray-500 uppercase tracking-widest font-medium">{{ $venue->name }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4 mx-8 mt-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-exclamation text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <ul class="list-disc list-inside text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('fields.update', [$venue->id, $field->id]) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
            @csrf
            @method('PUT') <div class="flex flex-col md:flex-row gap-10">
                
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
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Upload Foto</p>
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
                        <div class="absolute -bottom-3 -right-3 w-10 h-10 bg-emerald-600 rounded-full border-4 border-white flex items-center justify-center text-white shadow-lg">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </div>
                    </div>

                    <div class="mt-8">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Tipe Lantai</label>
                        <select name="field_type" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-4 py-4 text-sm font-medium focus:ring-emerald-500 focus:border-emerald-500 transition-all appearance-none">
                            @foreach(['Vinyl' => 'Vinyl / Karpet', 'Interlock' => 'Interlock', 'Parquet' => 'Kayu (Parquet)', 'Semen' => 'Semen / Beton'] as $val => $label)
                                <option value="{{ $val }}" {{ old('field_type', $field->field_type) == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="w-full md:w-3/5 space-y-6">
                    
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Nama Lapangan</label>
                        <input type="text" name="name" value="{{ old('name', $field->name) }}" placeholder="Contoh: Court 1" required
                            class="w-full bg-gray-50 border-gray-100 rounded-2xl px-4 py-4 text-sm font-medium focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Harga Reguler</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs">Rp</span>
                                <input type="number" name="price_regular" value="{{ old('price_regular', $field->price_regular) }}" placeholder="50.000" required
                                    class="w-full bg-gray-50 border-gray-100 rounded-2xl pl-10 pr-4 py-4 text-sm font-bold focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-emerald-500 uppercase tracking-widest mb-3">Harga Peak Time</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-300 font-bold text-xs">Rp</span>
                                <input type="number" name="price_peak" value="{{ old('price_peak', $field->price_peak) }}" placeholder="80.000" required
                                    class="w-full bg-emerald-50/30 border-emerald-100 rounded-2xl pl-10 pr-4 py-4 text-sm font-bold text-emerald-700 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Deskripsi / Catatan Lapangan</label>
                        <textarea name="description" rows="4" placeholder="Jelaskan keunggulan lapangan ini..."
                            class="w-full bg-gray-50 border-gray-100 rounded-2xl px-4 py-4 text-sm font-medium focus:ring-emerald-500 focus:border-emerald-500 transition-all">{{ old('description', $field->description) }}</textarea>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-emerald-100 transition-all active:scale-[0.98] uppercase tracking-widest text-xs flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex items-center gap-4 p-5 bg-white rounded-2xl border border-gray-50 shadow-sm">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500">
                <i class="fa-solid fa-lightbulb"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-700">Tips Optimasi</p>
                <p class="text-[11px] text-gray-500 leading-relaxed">Perubahan harga akan langsung diterapkan pada pesanan baru yang masuk.</p>
            </div>
        </div>
        <div class="flex items-center gap-4 p-5 bg-white rounded-2xl border border-gray-50 shadow-sm">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500">
                <i class="fa-solid fa-camera-rotate"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-700">Foto Berkualitas</p>
                <p class="text-[11px] text-gray-500 leading-relaxed">Ganti foto secara berkala untuk menunjukkan kondisi lapangan terbaru kepada pelanggan.</p>
            </div>
        </div>
    </div>
</div>
@endsection