@extends('layouts.user')

@section('content')
    <header class="bg-brand py-14 px-6 text-center text-white relative">
        <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 tracking-tight uppercase">Booking Lapangan Online Terbaik</h2>
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
                    <option value="">Pilih Cabor</option>
                    <option value="Padel">Padel</option>
                    <option value="Badminton">Badminton</option>
                    <option value="Futsal">Futsal</option>
                </select>
            </div>

            <button type="submit" class="bg-brand text-white px-10 py-4 rounded-xl font-bold text-sm w-full md:w-auto hover:bg-opacity-95 transition">
                Cari Venue
            </button>
        </form>
    </div>

    <main class="max-w-6xl mx-auto py-16 px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            @foreach($venues as $venue)
            <a href="{{ route('venue.detail', $venue->id) }}" class="group block">
                <div class="relative overflow-hidden rounded-3xl mb-4 shadow-sm group-hover:shadow-xl transition-all duration-500 border border-gray-100">
                    <img src="{{ $venue->image ? asset('storage/' . $venue->image) : asset('assets/images/default-venue.jpg') }}" 
                         class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-700" 
                         alt="{{ $venue->name }}">
                    
                    <div class="absolute top-4 left-4 bg-brand/90 backdrop-blur text-white text-[10px] font-bold px-4 py-1 rounded-full shadow-lg uppercase tracking-wider">
                        {{ $venue->category ?? 'Multisport' }}
                    </div>
                </div>
                
                <div class="px-2">
                    <h3 class="text-xl font-black text-gray-800 group-hover:text-brand transition-colors mb-1 uppercase italic">{{ $venue->name }}</h3>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="flex items-center gap-1 text-yellow-500 text-xs font-black">
                            <i class="fa fa-star"></i> 4.9
                        </span>
                        <span class="text-gray-400 text-xs font-medium italic">· {{ $venue->city ?? 'Yogyakarta' }}</span>
                    </div>
                    
                    <p class="text-gray-400 text-[10px] font-bold uppercase tracking-tighter">
                        Mulai Dari <span class="text-brand text-lg ml-1 font-black leading-none">Rp {{ number_format($venue->min_price ?? 35000, 0, ',', '.') }}</span>
                    </p>
                </div>
            </a>
            @endforeach
        </div>
    </main>
@endsection