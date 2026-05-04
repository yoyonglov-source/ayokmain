<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AyokMain - Booking Venue Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .bg-brand { background-color: #0d8173; }
        .text-brand { color: #0d8173; }
        .border-brand { border-color: #1e4d40; }
    </style>
    <style>
    .btn-primary-gradient {
        background: linear-gradient(to right, #0d4a51, #145d65);
        color: white;
        font-weight: 600;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .btn-primary-gradient:hover {
        background: linear-gradient(to right, #145d65, #0d4a51);
    }
    
    .btn-primary-gradient:active {
        transform: scale(0.95);
    }
</style>
</head>
<body class="bg-white">

    <nav class="bg-white border-b sticky top-0 z-50 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-10">
            <a href="{{ url('/') }}" class="flex items-center h-full">
                <img src="{{ asset('assets/images/ayokmain_logo.png') }}" 
                    alt="Logo AyokMain" 
                    class="h-10 w-auto object-contain py-0 my-0 block">
            </a>
            <div class="hidden md:flex gap-6 text-sm font-semibold text-gray-500">
                <a href="{{ url('/') }}" class="text-brand border-b-2 border-brand pb-1">Sewa Venue</a>
                <a href="#" class="hover:text-brand transition">Partner With Us</a>
            </div>
        </div>
        <div class="flex gap-3 items-center">
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-600 px-4">Dashboard</a>
            @else
                {{-- Gunakan route() bukan url() agar lebih aman --}}
                <a href="{{ route('login') }}" class="text-sm font-bold text-gray-600 px-3 uppercase">Masuk</a>
                <a href="{{ route('register') }}" class="bg-brand text-white px-4 py-2 rounded-lg text-sm font-bold hover:opacity-90 transition uppercase">Daftar</a>
            @endauth
        </div>
    </nav>

    @yield('content')

    <footer class="bg-gray-100 py-10 text-center border-t mt-20">
        <p class="text-gray-400 text-sm italic">© 2026 AyokMain. Platform Booking Olahraga Terintegrasi.</p>
    </footer>
</body>
</html>