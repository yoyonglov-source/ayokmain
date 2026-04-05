@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Gedung Saya</h1>
        <p class="text-sm text-gray-500">Kelola daftar venue olahraga yang Anda miliki</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        
        <a href="{{ route('venues.create') }}" class="group flex flex-col items-center justify-center p-8 bg-white border-2 border-dashed border-gray-200 rounded-3xl hover:border-emerald-500 hover:bg-emerald-50/30 transition-all duration-300 min-h-[350px]">
            <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-plus text-2xl text-emerald-600"></i>
            </div>
            <div class="mt-6 text-center">
                <h3 class="text-lg font-bold text-gray-800">Tambah Gedung Baru</h3>
                <p class="mt-2 text-sm text-gray-400 max-w-[200px] leading-relaxed">
                    Daftarkan venue baru untuk mulai menerima pesanan
                </p>
            </div>
        </a>

        @foreach($venues as $venue)
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group transition-all duration-300 hover:shadow-md relative">
            
            <a href="{{ route('venues.edit', $venue->id) }}" 
                class="absolute top-4 left-4 z-10 bg-white/95 backdrop-blur-sm p-2.5 rounded-xl shadow-md text-amber-500 
                    opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-300 active:scale-90">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
            </a>

            <div class="relative h-52 w-full bg-gray-100 overflow-hidden">
                <img src="{{ $venue->image ? asset('storage/' . $venue->image) : 'https://via.placeholder.com/600x400?text=No+Photo' }}" 
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                    alt="{{ $venue->name }}">
                
                @if($venue->is_active)
                <div class="absolute top-4 right-4 bg-emerald-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                    Aktif
                </div>
                @endif
            </div>

            <div class="p-6 flex flex-col flex-grow">
                <div class="mb-4">
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded uppercase tracking-wide">
                        {{ $venue->category }}
                    </span>
                    <h3 class="text-xl font-bold text-gray-800 mt-2 group-hover:text-emerald-700 transition-colors truncate">
                        {{ $venue->name }}
                    </h3>
                    <div class="flex items-center text-gray-500 text-sm mt-1">
                        <i class="fa-solid fa-location-dot text-emerald-600 mr-2"></i>
                        {{ $venue->city }}
                    </div>
                </div>

                <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between">
                    <span class="text-xs text-gray-400 font-medium">
                        <i class="fa-solid fa-layer-group mr-1"></i> {{ $venue->fields_count }} Lapangan
                    </span>
                    {{-- Link Kelola Detail Tetap Ada sebagai Tombol Utama --}}
                    <a href="{{ route('venues.fields.index', $venue->id) }}" class="text-emerald-700 font-bold text-sm hover:text-emerald-800 flex items-center">
                        Kelola Detail <i class="fa-solid fa-chevron-right ml-2 text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-12">
        <x-venue-footer-tips />
    </div>
</div>
@endsection