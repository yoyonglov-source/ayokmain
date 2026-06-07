<x-guest-layout>
    <div class="max-w-md mx-auto my-12 bg-white p-8 rounded-xl shadow-md border border-gray-100 text-center">
        <!-- Icon Jam / Loading Animasi -->
        <div class="inline-flex items-center justify-center w-16 h-16 bg-teal-50 rounded-full text-teal-600 mb-6 animate-pulse">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Pendaftaran Sedang Ditinjau!</h2>
        
        <p class="text-sm text-gray-600 mt-3 leading-relaxed">
            Terima kasih telah mendaftar di <span class="font-semibold text-teal-600">AyokMain</span>. Data identitas KYC dan lokasi Google Maps gedung Anda saat ini sedang diperiksa oleh tim tim kurasi kami untuk mencegah penipuan.
        </p>

        <div class="mt-6 bg-gray-50 p-4 rounded-lg border border-gray-100 text-left text-xs text-gray-500 space-y-2">
            <div class="flex items-start">
                <span class="text-teal-500 mr-2">✔</span>
                <span>Proses verifikasi maps & dokumen memakan waktu maksimal 1x24 jam.</span>
            </div>
            <div class="flex items-start">
                <span class="text-teal-500 mr-2">✔</span>
                <span>Notifikasi persetujuan akun akan dikirimkan melalui email resmi Anda.</span>
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('user.home') }}" class="inline-block bg-gray-800 hover:bg-gray-900 text-white font-semibold py-2.5 px-6 rounded-lg transition duration-200 text-sm">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</x-guest-layout>