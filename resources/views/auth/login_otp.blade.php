@extends('layouts.user')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-6" x-data="authSystem()">
    <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm max-w-md w-full space-y-6">
        
        <div class="text-center">
            <h1 class="text-xl font-black text-gray-850 uppercase tracking-wide">Masuk Ke Akun</h1>
            <p class="text-xs text-gray-400 mt-1">Verifikasi cepat untuk melihat tiket & riwayat booking Anda.</p>
        </div>

        <!-- Alert Error Global -->
        <div x-show="authError" x-transition class="bg-red-50 border border-red-200 text-red-600 text-xs font-bold p-4 rounded-2xl flex items-center gap-2">
            <span>⚠️</span> <span x-text="authError"></span>
        </div>

        <!-- STEP 1: INPUT NOMOR HP -->
        <div x-show="authStep === 'phone'" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nomor WhatsApp</label>
                <input type="tel" x-model="authPhone" placeholder="08123456xxx" class="w-full bg-gray-50 border border-gray-200 rounded-2xl p-4 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
            </div>
            <button type="button" @click="handleRequestOtp()" :disabled="authLoading" class="w-full bg-[#0d8173] hover:bg-[#0a665b] text-white font-bold py-4 rounded-2xl transition flex items-center justify-center gap-2 text-sm uppercase tracking-wider">
                <span x-show="authLoading" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
                <span x-text="authLoading ? 'Mengirim...' : 'Kirim Kode OTP WA'"></span>
            </button>
        </div>

        <!-- STEP 2: INPUT OTP (+ TIMER EMAlL DARURAT) -->
        <div x-show="authStep === 'otp'" class="space-y-4">
            <p class="text-xs text-gray-500 text-center">Kode OTP dikirim ke <span class="font-bold text-gray-750" x-text="authPhone"></span></p>
            <div>
                <input type="text" x-model="authOtp" maxlength="4" placeholder="0000" class="w-full bg-gray-50 border border-gray-200 rounded-2xl p-4 text-center text-2xl font-black tracking-widest focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
            </div>
            <button type="button" @click="handleVerifyOtp()" :disabled="authLoading" class="w-full bg-[#0d8173] hover:bg-[#0a665b] text-white font-bold py-4 rounded-2xl transition flex items-center justify-center gap-2 text-sm uppercase tracking-wider">
                <span x-show="authLoading" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
                <span x-text="authLoading ? 'Memverifikasi...' : 'Verifikasi & Masuk'"></span>
            </button>

            <!-- Link Pintu Darurat Email -->
            <div class="text-center pt-1" x-show="showEmailFallback" x-transition>
                <button type="button" @click="authStep = 'fallback_email'; authError = ''" class="text-xs text-red-600 hover:underline font-bold">
                    ⚠️ WA Belum Masuk? Kirim via Email
                </button>
            </div>
            <div class="text-center pt-1" x-show="!showEmailFallback">
                <span class="text-[10px] text-gray-400">Tunggu <span class="font-bold" x-text="countdownToEmail"></span> detik untuk opsi Email</span>
            </div>
        </div>

        <!-- STEP 3: FALLBACK EMAIL -->
        <div x-show="authStep === 'fallback_email'" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email Akun Anda</label>
                <input type="email" x-model="authFallbackEmail" placeholder="nama@email.com" class="w-full bg-gray-50 border border-gray-200 rounded-2xl p-4 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
            </div>
            <button type="button" @click="handleSendOtpEmail()" :disabled="authLoading" class="w-full bg-gray-800 hover:bg-black text-white font-bold py-4 rounded-2xl transition flex items-center justify-center gap-2 text-sm uppercase tracking-wider">
                <span x-show="authLoading" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
                <span x-text="authLoading ? 'Mengirim...' : 'Kirim Kode ke Email'"></span>
            </button>
        </div>

    </div>
</div>

<script>
function authSystem() {
    return {
        authStep: 'phone',
        authPhone: '',
        authOtp: '',
        authFallbackEmail: '',
        authLoading: false,
        authError: '',
        showEmailFallback: false,
        countdownToEmail: 15,

        async handleRequestOtp() {
            if(!this.authPhone) { this.authError = 'Nomor HP wajib diisi!'; return; }
            this.authLoading = true; this.authError = '';
            
            try {
                // Tembak route kirim OTP yang biasa digunakan di checkout kemarin
                let response = await fetch('/checkout/send-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ phone: this.authPhone })
                });
                let result = await response.json();
                if (response.ok) {
                    this.authStep = 'otp';
                    this.startTimer();
                } else { this.authError = result.message || 'Gagal mengirim OTP.'; }
            } catch (e) { this.authError = 'Terjadi kesalahan server.'; }
            finally { this.authLoading = false; }
        },

        startTimer() {
            this.showEmailFallback = false;
            this.countdownToEmail = 15;
            let timer = setInterval(() => {
                if (this.countdownToEmail > 1) { this.countdownToEmail--; } 
                else { this.showEmailFallback = true; clearInterval(timer); }
            }, 1000);
        },

        async handleVerifyOtp() {
            if(!this.authOtp) { this.authError = 'Kode OTP wajib diisi!'; return; }
            this.authLoading = true; this.authError = '';

            try {
                let response = await fetch('/auth/verify-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ phone: this.authPhone, otp: this.authOtp,action: 'login'})
                });
                let result = await response.json();
                if (response.ok) {
                    // Jika sukses verifikasi, langsung lempar kembali ke halaman riwayat booking
                    window.location.href = '{{ route("user.bookings.index") }}';
                } else { this.authError = result.message; }
            } catch (e) { this.authError = 'Verifikasi gagal.'; }
            finally { this.authLoading = false; }
        },

        async handleSendOtpEmail() {
            if(!this.authFallbackEmail) { this.authError = 'Email wajib diisi!'; return; }
            this.authLoading = true; this.authError = '';
            try {
                let response = await fetch('/checkout/send-otp-email', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ email: this.authFallbackEmail })
                });
                if (response.ok) {
                    alert('OTP dialihkan ke email!');
                    this.authStep = 'otp';
                } else { this.authError = 'Gagal mengirim email.'; }
            } catch(e) { this.authError = 'Sistem error.'; }
            finally { this.authLoading = false; }
        }
    }
}
</script>
@endsection