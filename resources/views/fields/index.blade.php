@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2">
                <a href="{{ route('venues.index') }}" class="hover:text-emerald-600 transition-colors">Gedung Saya</a>
                <span class="mx-2">/</span>
                <span class="text-gray-800 font-medium">{{ $venue->name }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Lapangan</h1>
            <p class="text-sm text-gray-500">Atur detail dan harga lapangan di {{ $venue->name }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Lapangan</p>
            <p class="text-xl font-black text-gray-800">{{ $venue->fields_count }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-emerald-500">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aktif</p>
            <p class="text-xl font-black text-emerald-600">{{ $venue->fields()->where('is_active', true)->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <a href="{{ route('fields.create', $venue->id) }}" class="group relative flex flex-col items-center justify-center bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl p-8 transition-all hover:border-emerald-500 hover:bg-emerald-50/30 min-h-[300px]">
            <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 mb-4 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-plus text-emerald-600 text-xl"></i>
            </div>
            <span class="text-sm font-bold text-gray-700">Tambah Lapangan Baru</span>
            <p class="text-[10px] text-gray-400 mt-2 text-center px-4">Klik untuk menambah lapangan pada gedung ini</p>
        </a>

        @foreach($venue->fields as $field)
        <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden hover:shadow-md transition-all group min-h-[300px] flex flex-col">
            <div class="relative h-40 bg-gray-200">
                <img src="{{ $field->image ? asset('storage/' . $field->image) : 'https://via.placeholder.com/400x200?text=No+Photo' }}" 
                     class="w-full h-full object-cover transition-transform group-hover:scale-105 duration-500">
                
                <div class="absolute bottom-3 left-3">
                    <span class="bg-white/90 backdrop-blur-sm text-gray-800 text-[10px] font-bold px-2 py-1 rounded-md shadow-sm uppercase">
                        <i class="fa-solid fa-layer-group text-emerald-500 mr-1"></i> {{ $field->field_type }}
                    </span>
                </div>
            </div>

            <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-bold text-gray-800">{{ $field->name }}</h3>
                            <div class="flex items-center mt-1">
                                <div class="w-2 h-2 rounded-full {{ $field->is_active ? 'bg-emerald-500' : 'bg-amber-500' }} mr-2"></div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                                    {{ $field->is_active ? 'Tersedia' : 'Maintenance' }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('fields.edit', [$venue->id, $field->id]) }}" class="text-gray-300 hover:text-amber-500 transition-colors">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-3 bg-gray-50 rounded-2xl">
                        <div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Reguler</p>
                            <p class="text-sm font-black text-gray-700">Rp{{ number_format($field->price_regular, 0, ',', '.') }}</p>
                        </div>
                        <div class="border-l border-gray-200 pl-3">
                            <p class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Peak Time</p>
                            <p class="text-sm font-black text-emerald-700">Rp{{ number_format($field->price_peak, 0, ',', '.') }}</p>
                        </div>
                    </div>
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