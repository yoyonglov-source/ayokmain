<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MainYuk</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-emerald-700 text-white flex flex-col">

        <!-- Logo -->
        <div class="px-6 py-5 text-lg font-bold border-b border-emerald-600">
            🏸 MainYuk
            <div class="text-xs opacity-70">Admin Partner</div>
        </div>

        <!-- Menu -->
        <nav class="flex-1 px-4 py-6 space-y-2 text-sm">

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-emerald-600">
                Dashboard
            </a>

            <a href="/venues" class="block px-4 py-2 rounded-lg bg-emerald-600">
                Gedung Saya
            </a>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-emerald-600">
                Booking Masuk
            </a>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-emerald-600">
                Laporan Keuangan
            </a>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-emerald-600">
                Manajemen Staff
            </a>

            <div class="mt-6 text-xs opacity-60 px-4">
                AKUN
            </div>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-emerald-600">
                Pengaturan
            </a>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-emerald-600">
                Bantuan
            </a>

        </nav>

        <!-- User -->
        <div class="p-4 border-t border-emerald-600">
            <div class="text-sm font-semibold">
                {{ auth()->user()->name ?? 'User' }}
            </div>
        </div>

    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col">

        <!-- HEADER -->
        <header class="bg-white border-b px-6 py-4 flex justify-between items-center">

            <div class="font-semibold text-gray-700">
                Dashboard
            </div>

        </header>

        <!-- CONTENT -->
        <main class="p-6">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>