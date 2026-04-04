@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <a href="/venues/create" class="group flex flex-col items-center justify-center p-8 bg-white border-2 border-dashed border-gray-300 rounded-3xl hover:border-emerald-500 hover:bg-emerald-50/30 transition-all duration-300 min-h-[320px]">
        <div class="w-16 h-16 bg-emerald-100/50 text-emerald-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
            <i class="fa-solid fa-plus text-2xl"></i>
        </div>
        <div class="mt-6 text-center">
            <h3 class="text-lg font-bold text-gray-800">Tambah Gedung Baru</h3>
            <p class="mt-2 text-sm text-gray-500 max-w-[200px] leading-relaxed">
                Daftarkan venue baru untuk mulai menerima pesanan
            </p>
        </div>
    </a>

    @foreach($venues as $venue)
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group transition-all duration-300 hover:shadow-md">
        <div class="relative h-48 w-full">
            <img src="{{ $venue->image_url ?? 'https://via.placeholder.com/400x300' }}" class="w-full h-full object-cover" alt="{{ $venue->name }}">
            <div class="absolute top-4 right-4 bg-emerald-500 text-white text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wider">
                Aktif
            </div>
        </div>

        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-800 group-hover:text-emerald-700 transition-colors">{{ $venue->name }}</h3>
            <div class="flex items-center text-gray-500 text-sm mt-1">
                <i class="fa-solid fa-location-dot text-emerald-600 mr-2"></i>
                {{ $venue->location }}
            </div>

            <div class="mt-6 pt-4 border-t border-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-400 font-medium">4 Lapangan</span>
                <a href="/venues/{{ $venue->id }}" class="text-emerald-700 font-bold text-sm hover:underline">
                    Kelola Detail
                </a>
            </div>
        </div>
    </div>
    @endforeach

</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
    <div class="bg-white p-6 rounded-2xl border border-gray-100 flex items-start space-x-4">
        <div class="bg-amber-100 p-3 rounded-xl text-amber-600"><i class="fa-solid fa-image"></i></div>
        <div>
            <h4 class="font-bold text-gray-800 text-sm">Foto Menarik</h4>
            <p class="text-xs text-gray-500 mt-1">Gunakan foto berkualitas tinggi untuk menarik minat pelanggan.</p>
        </div>
    </div>
    </div>
@endsection