<x-guest-layout>
    <div class="max-w-2xl mx-auto my-6 bg-white p-8 rounded-xl shadow-md border border-gray-100" x-data="ownerRegister()">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">DAFTAR SEBAGAI OWNER GOR</h2>
            <p class="text-sm text-gray-500 mt-1">Lengkapi data diri & gedung Anda untuk mulai menerima booking online.</p>
        </div>

        <form method="POST" action="{{ route('register.owner.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- BAGIAN 1: AKUN UTAMA -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200/60 space-y-4">
                <h3 class="text-sm font-semibold text-teal-700 tracking-wide uppercase">1. Informasi Akun Utama</h3>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nama Lengkap Sesuai KTP</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-teal-500 text-sm">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Email Aktif</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-teal-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Password</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-teal-500 text-sm">
                    </div>
                </div>
            </div>

            <!-- BAGIAN 2: VERIFIKASI IDENTITAS (ANTI PENIPUAN) -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200/60 space-y-4">
                <h3 class="text-sm font-semibold text-teal-700 tracking-wide uppercase">2. Verifikasi Dokumen (KYC)</h3>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nomor NIK KTP (16 Digit)</label>
                    <input type="text" name="ktp_number" maxlength="16" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-teal-500 text-sm tracking-widest" placeholder="3507xxxxxxxxxxxx">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- JEPRET KTP -->
                    <div class="bg-white p-3 border rounded-lg shadow-sm text-center">
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Foto KTP Asli</label>
                        <input type="file" accept="image/*" capture="environment" name="ktp_photo" required class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100"/>
                        <p class="text-[10px] text-gray-400 mt-2">Pastikan teks KTP terbaca jelas & tidak blur.</p>
                    </div>

                    <!-- JEPRET SELFIE -->
                    <div class="bg-white p-3 border rounded-lg shadow-sm text-center">
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-2">Selfie Memegang KTP</label>
                        <input type="file" accept="image/*" capture="user" name="selfie_photo" required class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100"/>
                        <p class="text-[10px] text-gray-400 mt-2">Wajah dan KTP harus terlihat dalam satu frame.</p>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 3: DATA GEDUNG GOR AWAL -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200/60 space-y-4">
                <h3 class="text-sm font-semibold text-teal-700 tracking-wide uppercase">3. Informasi Gedung Olahraga (GOR)</h3>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nama GOR / Gedung</label>
                    <input type="text" name="venue_name" placeholder="Contoh: Gracia Badminton Center" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-teal-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Link URL Google Maps Lapangan</label>
                    <input type="url" name="google_maps_url" placeholder="https://maps.google.com/..." required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-teal-500 text-sm">
                    <p class="text-[11px] text-gray-400 mt-1">Penting: Tim AyokMain akan melakukan pengecekan lewat titik koordinat ini.</p>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nomor Kontak GOR (WhatsApp)</label>
                    <input type="text" name="phone_number" placeholder="08123456xxx" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-teal-500 text-sm">
                </div>
            </div>

            <!-- BUTTON REGISTRASI -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 text-sm tracking-wide uppercase">
                    Ajukan Kemitraan Owner
                </button>
            </div>
        </form>
    </div>

    <script>
        function ownerRegister() {
            return {
                // AlpineJS data jika dibutuhkan manipulasi state kedepannya
            }
        }
    </script>
</x-guest-layout>