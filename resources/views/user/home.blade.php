<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AyokMain - Booking Lapangan Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Warna Dasar sesuai referensi gambar Anda */
        .bg-brand { background-color: #1e4d40; } /* Hijau Tua Sporty */
        .text-brand { color: #1e4d40; }
        .border-brand { border-color: #1e4d40; }
        .bg-brand-light { background-color: #f1f5f4; }
        .btn-green { background-color: #2d6a58; }
    </style>
</head>
<body class="bg-white">

    <nav class="bg-white border-b sticky top-0 z-50 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-10">
            <h1 class="text-2xl font-black text-brand tracking-tighter italic">AYOK<span class="text-gray-400">MAIN</span></h1>
            <div class="hidden md:flex gap-6 text-sm font-semibold text-gray-500">
                <a href="#" class="text-brand border-b-2 border-brand pb-1">Sewa Lapangan</a>
                <a href="#" class="hover:text-brand transition">Partner With Us</a>
                <a href="#" class="hover:text-brand transition">Liga AYO</a>
            </div>
        </div>
        <div class="flex gap-3 items-center">
            <button class="text-sm font-bold text-gray-600 px-4">Masuk</button>
            <button class="bg-brand text-white px-6 py-2 rounded-lg text-sm font-bold hover:opacity-90 transition">Daftar</button>
        </div>
    </nav>

    <header class="bg-brand py-14 px-6 text-center text-white">
        <h2 class="text-3xl md:text-4xl font-bold mb-4 tracking-tight uppercase">Booking Lapangan Online Terbaik</h2>
        <button class="bg-yellow-400 text-brand px-6 py-2 rounded-full text-xs font-black uppercase shadow-lg hover:scale-105 transition-transform">Daftarkan Venue ➜</button>
    </header>

    <div class="max-w-6xl mx-auto -mt-10 px-4 relative z-20">
        <div class="bg-white p-2 rounded-2xl shadow-2xl flex flex-wrap md:flex-nowrap gap-2 items-center border border-gray-100">
            <div class="flex-1 min-w-[200px] relative">
                <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Cari nama venue..." class="w-full pl-11 pr-4 py-4 rounded-xl bg-gray-50 text-sm outline-none border-transparent focus:bg-white focus:ring-1 focus:ring-brand transition">
            </div>
            <div class="w-full md:w-56 relative text-gray-500">
                <i class="fa fa-location-dot absolute left-4 top-1/2 -translate-y-1/2"></i>
                <select class="w-full pl-11 pr-4 py-4 rounded-xl bg-gray-50 text-sm outline-none appearance-none border-none">
                    <option>Kota Yogyakarta</option>
                </select>
            </div>
            <div class="w-full md:w-56 relative text-gray-500">
                <i class="fa fa-volleyball absolute left-4 top-1/2 -translate-y-1/2"></i>
                <select class="w-full pl-11 pr-4 py-4 rounded-xl bg-gray-50 text-sm outline-none appearance-none border-none">
                    <option>Pilih Cabang Olahraga</option>
                </select>
            </div>
            <button class="bg-brand text-white px-10 py-4 rounded-xl font-bold text-sm w-full md:w-auto hover:bg-opacity-95 transition">Cari Venue</button>
        </div>
    </div>

    <main class="max-w-6xl mx-auto py-16 px-6">
        <div class="flex justify-between items-center mb-10">
            <p class="text-gray-400 text-sm">Menampilkan <span class="font-bold text-gray-800">{{ $venues->count() }} venue tersedia</span></p>
            <div class="flex items-center gap-2 text-xs font-bold text-gray-500">
                URUTKAN: <span class="text-brand cursor-pointer">POPULERITAS <i class="fa fa-chevron-down ml-1"></i></span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            @foreach($venues as $venue)
            <a href="{{ route('venue.detail', $venue->id) }}" class="group block">
                <div class="relative overflow-hidden rounded-2xl mb-4 shadow-sm group-hover:shadow-xl transition-all duration-500">
                    <img src="https://images.unsplash.com/photo-1599586120429-48281b6f0ece?q=80&w=1000" class="w-full h-60 object-cover group-hover:scale-105 transition-transform duration-700" alt="{{ $venue->name }}">
                    <div class="absolute top-4 left-4 bg-brand text-white text-[10px] font-bold px-3 py-1 rounded-md shadow-lg">
                        {{ $venue->type ?? 'PADEL' }}
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold text-gray-800 group-hover:text-brand transition-colors mb-1">{{ $venue->name }}</h3>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="flex items-center gap-1 text-yellow-500 text-xs font-black">
                            <i class="fa fa-star"></i> 4.9
                        </span>
                        <span class="text-gray-400 text-xs font-medium italic">· Yogyakarta</span>
                    </div>
                    
                    <div class="flex items-center justify-between mt-4">
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-tighter">Mulai <span class="text-brand text-lg ml-1 font-black leading-none">Rp {{ number_format($venue->min_price ?? 35000, 0, ',', '.') }}</span><span class="text-[10px] lowercase font-normal italic">/ sesi</span></p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </main>

</body>
</html>