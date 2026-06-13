<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AyokMain</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen relative">

    <div x-show="sidebarOpen" 
         x-transition.opacity
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"
         style="display: none;"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-50 w-72 bg-emerald-800 text-white flex flex-col shadow-xl transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0">

        <div class="px-6 py-6 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white p-2 rounded-lg">
                    <i class="fa-solid fa-trophy text-emerald-700 text-xl"></i>
                </div>
                <div>
                    <div class="font-bold text-lg leading-none">SportVenue</div>
                    <div class="text-[10px] opacity-70 uppercase tracking-wider">Admin Partner</div>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-white/70 hover:text-white">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1 text-sm overflow-y-auto">

            <a href="{{ route('dashboard') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-emerald-600/40 border border-emerald-500/30 font-medium' : 'hover:bg-emerald-700/50 opacity-70' }}">
                    <i class="fa-solid fa-gauge-high w-5"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.venues.index') }}" 
                class="flex items-center space-x-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.venues.*') ? 'bg-emerald-600/40 border border-emerald-500/30 font-medium' : 'hover:bg-emerald-700/50 opacity-70' }}">
                    <i class="fa-solid fa-building w-5"></i>
                    <span>Gedung Saya</span>
                </a>

            <a href="{{ route('admin.booking.history') }}" class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-emerald-700/50 transition group">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-calendar-check opacity-70 w-5"></i>
                    <span>Booking Masuk</span>
                </div>
            </a>

            <a href="{{ route('admin.financial.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-emerald-700/50 transition border-b border-emerald-700/50 pb-4 mb-2">
                <i class="fa-solid fa-money-bill-trend-up opacity-70 w-5"></i>
                <span>Laporan Keuangan</span>
            </a>

            <a href="{{ route('admin.staff.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-emerald-700/50 transition border-b border-emerald-700/50 pb-4 mb-2">
                <i class="fa-solid fa-users-gear opacity-70 w-5"></i>
                <span>Manajemen Staff</span>
            </a>

            <div class="pt-4 pb-2 px-4 text-[10px] font-bold opacity-40 uppercase tracking-[0.2em]">
                AKUN
            </div>

            <div x-data="{ openSettings: {{ request()->is('settings*') ? 'true' : 'false' }} }">
                <button @click="openSettings = !openSettings" 
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-emerald-700/50 transition group {{ request()->is('settings*') ? 'bg-emerald-700/30' : '' }}">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-gear opacity-70 w-5"></i>
                        <span>Pengaturan</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="openSettings ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="openSettings" 
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    class="mt-1 ml-4 border-l border-emerald-700/50 pl-4 space-y-1">
                    
                    <a href="{{ route('settings.operating-hours') }}" 
                    class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-emerald-700/30 transition text-xs {{ request()->routeIs('settings.operating-hours') ? 'text-white font-bold bg-emerald-600/20' : 'text-emerald-100/70' }}">
                        <span>Jam & Harga Lapangan</span>
                    </a>

                    <a href="{{ route('settings.payment-schema') }}" 
                    class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-emerald-700/30 transition text-xs {{ request()->routeIs('settings.payment-schema') ? 'text-white font-bold bg-emerald-600/20' : 'text-emerald-100/70' }}">
                        <span>Skema Pembayaran</span>
                    </a>
                </div>
            </div>

        </nav>

        <div class="p-4 m-4 bg-emerald-900/40 rounded-2xl border border-emerald-700/30 flex items-center justify-between group cursor-pointer hover:bg-emerald-900/60 transition">
            <div class="flex items-center space-x-3">
                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'User' }}&background=random" class="w-10 h-10 rounded-xl border-2 border-emerald-500/30" alt="Avatar">
                <div class="overflow-hidden">
                    <div class="text-xs font-bold truncate">{{ auth()->user()->name ?? 'Budi Santoso' }}</div>
                    <div class="text-[10px] opacity-60 truncate">Owner Gor A</div>
                </div>
            </div>
            <i class="fa-solid fa-right-from-bracket text-xs opacity-40 group-hover:opacity-100 transition"></i>
        </div>

    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <header class="bg-white px-4 lg:px-8 py-4 flex justify-between items-center shadow-sm">
            <div class="flex items-center space-x-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    <i class="fa-solid fa-bars-staggered text-xl"></i>
                </button>
                
                <div class="flex flex-col">
                    <h1 class="text-lg lg:text-xl font-bold text-gray-800 leading-tight">
                        @if(request()->routeIs('dashboard'))
                            Dashboard Utama
                        @elseif(request()->routeIs('admin.financial.index'))
                            Laporan Keuangan
                        @elseif(request()->routeIs('admin.booking.history'))
                            Riwayat Booking    
                        @elseif(request()->routeIs('admin.staff.*')) {{-- 👥 Tambahan kondisi di sini --}}
                            Manajemen Staff
                        @else
                            Gedung Saya
                        @endif
                    </h1>
                    <p class="hidden sm:block text-[11px] text-gray-400">
                        @if(request()->routeIs('dashboard'))
                            Ringkasan performa bisnis GOR Anda bulan ini
                        @elseif(request()->routeIs('admin.financial.index'))
                            Analisis menyeluruh pendapatan bisnis GOR Anda
                        @elseif(request()->routeIs('admin.booking.history'))
                            Pantau dan telusuri seluruh riwayat transaksi serta jadwal sewa lapangan
                        @elseif(request()->routeIs('admin.staff.*')) {{-- 👥 Tambahan sub-title di sini --}}
                            Kelola akun dan hak akses pegawai atau kasir GOR Anda
                        @else
                            Kelola daftar venue olahraga yang Anda miliki
                        @endif
                    </p>
                </div>
            </div>
        </header>

        <main class="p-4 lg:p-8 overflow-y-auto bg-gray-50 flex-1">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>