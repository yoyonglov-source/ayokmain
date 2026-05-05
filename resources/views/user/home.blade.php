@extends('layouts.user')

@section('content')
    <header class="bg-brand py-14 px-6 text-center text-white relative">
        <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 tracking-tight uppercase">Booking Venue Online</h2>
            <button class="bg-yellow-400 text-brand px-6 py-2 rounded-full text-xs font-black uppercase shadow-lg hover:scale-105 transition-transform">
                Daftarkan Venue ➜
            </button>
        </div>
    </header>

    <div class="max-w-6xl mx-auto -mt-10 px-4 relative z-20">
        <form action="{{ route('user.home') }}" method="GET" class="bg-white p-2 rounded-2xl shadow-2xl flex flex-wrap md:flex-nowrap gap-2 items-center border border-gray-100">
            <div class="flex-1 min-w-[200px] relative">
                <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" placeholder="Cari nama venue..." class="w-full pl-11 pr-4 py-4 rounded-xl bg-gray-50 text-sm outline-none border-transparent focus:bg-white focus:ring-1 focus:ring-brand transition">
            </div>

            <div class="w-full md:w-48 relative text-gray-500">
                <i class="fa fa-location-dot absolute left-4 top-1/2 -translate-y-1/2"></i>
                <select name="city" class="w-full pl-11 pr-4 py-4 rounded-xl bg-gray-50 text-sm outline-none appearance-none border-none">
                    <option value="">Semua Kota</option>
                    <option value="Yogyakarta">Yogyakarta</option>
                    <option value="Malang">Malang</option>
                </select>
            </div>

            <div class="w-full md:w-56 relative text-gray-500">
                <i class="fa fa-volleyball absolute left-4 top-1/2 -translate-y-1/2"></i>
                <select name="type" class="w-full pl-11 pr-4 py-4 rounded-xl bg-gray-50 text-sm outline-none appearance-none border-none font-semibold">
                    <option value="">Pilih Kategori</option>
                    <option value="Padel">Padel</option>
                    <option value="Badminton">Badminton</option>
                    <option value="Futsal">Futsal</option>
                    <option value="Futsal">Studio Musik</option>
                    <option value="Futsal">Gedung Pernikahan</option>
                </select>
            </div>

            <button type="submit" class="btn-primary-gradient w-full md:w-auto px-8 py-3">
                Cari Venue
            </button>
        </form>
    </div>

    <main class="max-w-6xl mx-auto py-16 px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            @foreach($venues as $venue)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden group">
                <div class="relative h-52 overflow-hidden">
                    <img src="{{ $venue->image ? asset('storage/' . $venue->image) : 'https://via.placeholder.com/400x300?text=No+Image' }}" 
                        alt="{{ $venue->name }}" 
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    
                    <div class="absolute top-4 left-4 bg-black/50 backdrop-blur-md text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        {{ $venue->category ?? 'Multi-Sport' }}
                    </div>
                </div>

                <div class="p-5">
                    <h3 class="text-lg font-bold text-gray-700 leading-tight mb-1 uppercase">
                        {{ $venue->name }}
                    </h3>
                    
                    <div class="flex items-center text-gray-500 text-sm mb-4">
                        <i class="fa-solid fa-location-dot mr-1.5 text-emerald-500"></i>
                        <span class="truncate">{{ $venue->city }}</span>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Mulai Dari</p>
                            <p class="text-emerald-600 font-extrabold text-lg">
                                Rp {{ number_format($venue->fields->min('price_regular') ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        
                        <a href="{{ route('venue.detail', $venue->id) }}" 
                            class="bg-[#0d8173] hover:bg-[#065f55] text-white w-10 h-10 rounded-full flex items-center justify-center transition-all shadow-md shadow-emerald-100">
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </main>
@endsection