@extends('layouts.user')

@section('content')
<div class="bg-[#0d8173] py-3 shadow-sm border-t border-white/10">
    <div class="max-w-6xl mx-auto px-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-[12px] font-medium uppercase tracking-wider">
                <li class="inline-flex items-center">
                    <a href="{{ url('/') }}" class="text-white/80 hover:text-white transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-house text-[10px]"></i> Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center text-white/50">
                        <i class="fa-solid fa-chevron-right text-[8px] mx-2"></i>
                        <span class="text-white/80">Sewa Venue</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center text-white/50">
                        <i class="fa-solid fa-chevron-right text-[8px] mx-2"></i>
                        <span class="text-white font-bold tracking-widest">{{ $venue->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<main class="max-w-6xl mx-auto py-10 px-6" x-data="bookingSystem()">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm">
                <div class="relative group">
                    <img src="{{ asset('storage/' . $venue->image) }}" class="w-full h-80 object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                    <div class="absolute bottom-6 left-6 text-white text-left">
                        <h1 class="text-4xl font-black italic uppercase tracking-tighter">{{ $venue->name }}</h1>
                        <p class="text-white/90 text-sm mt-1 flex items-center gap-2">
                            <i class="fa fa-map-marker-alt text-[#0d8173] bg-white p-1.5 rounded-md text-[10px]"></i> 
                            {{ $venue->address }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <aside>
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 h-[320px] flex flex-col text-left">
                <h3 class="text-xs font-black text-gray-400 mb-4 uppercase tracking-widest border-b pb-2">Deskripsi Venue</h3>
                <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar text-sm leading-relaxed text-gray-600">
                    {!! $venue->description ?? '<p class="italic text-gray-400">Belum ada deskripsi.</p>' !!}
                </div>
            </div>
        </aside>
    </div>

    <div class="mt-16">
        <h3 class="text-xl font-black text-gray-800 uppercase italic mb-8 flex items-center gap-3">
            <i class="fa-solid fa-play text-[#0d8173] text-xs"></i> Pilih Lapangan
        </h3>

        <div class="flex items-center gap-3 mb-8">
            <div class="flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar">
                <template x-for="date in availableDates" :key="date.fullDate">
                    <button @click="changeDate(date.fullDate)"
                        class="flex-shrink-0 w-20 py-3 rounded-2xl border-2 transition-all flex flex-col items-center justify-center gap-1"
                        :class="selectedDate === date.fullDate ? 'bg-[#991b1b] border-[#991b1b] text-white shadow-lg' : 'bg-white border-gray-100 text-gray-400 hover:border-gray-300'">
                        <span class="text-[9px] font-bold uppercase" x-text="date.dayName"></span>
                        <span class="text-lg font-black" x-text="date.dayNum"></span>
                        <span class="text-[9px] font-bold uppercase" x-text="date.monthName"></span>
                    </button>
                </template>
            </div>
            
            <div class="h-12 w-[1px] bg-gray-200 mx-2"></div>
            
            <div class="relative">
                <button type="button" @click="openPicker()" class="w-14 h-14 bg-white border-2 border-gray-100 rounded-2xl flex items-center justify-center text-gray-400 hover:border-[#991b1b] transition-all">
                    <i class="fa-regular fa-calendar-days text-xl"></i>
                </button>
                <input type="text" id="datepicker" class="absolute inset-0 opacity-0 pointer-events-none">
            </div>
        </div>

        <div class="space-y-6"> <!-- Container Utama -->
            @foreach($fields as $field)
                <div class="p-6 flex flex-col md:flex-row gap-8">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('storage/' . $field->image) }}" class="w-full md:w-72 h-48 object-cover rounded-3xl shadow-md">
                    </div>
                    
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Nama Lapangan -->
                            <h3 class="font-black italic text-3xl uppercase tracking-tighter text-gray-800">
                                {{ $field->name }}
                            </h3>
                            
                            <!-- Informasi Kategori & Lantai -->
                            <div class="mt-3 flex flex-wrap gap-4 items-center">
                                <!-- Kategori dari Venue (category) -->
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-lg">
                                    <i class="fa-solid fa-tag text-[#0d8173] text-[10px]"></i>
                                    <span class="text-[11px] font-black uppercase tracking-wider text-gray-600">
                                        {{ $venue->category }}
                                    </span>
                                </div>

                                <!-- Tipe Lantai dari Field (field_type) -->
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-lg">
                                    <i class="fa-solid fa-layer-group text-[#0d8173] text-[10px]"></i>
                                    <span class="text-[11px] font-black uppercase tracking-wider text-gray-600">
                                        {{ $field->field_type }}
                                    </span>
                                </div>
                            </div>

                            <!-- Deskripsi Singkat (Opsional) -->
                            @if($field->description)
                                <p class="mt-4 text-sm text-gray-500 leading-relaxed max-w-xl">
                                    {{ Str::limit($field->description, 100) }}
                                </p>
                            @endif
                        </div>

                        <!-- Tombol Toggle Jadwal -->
                        <div class="mt-6">
                            <button @click="toggleJadwal({{ $field->id }}, '{{ $field->name }}')" 
                                class="bg-[#991b1b] text-white px-8 py-3.5 rounded-2xl font-black uppercase text-xs tracking-widest flex items-center gap-3 hover:bg-red-900 transition-all shadow-lg shadow-red-900/20">
                                <span x-text="openFieldId === {{ $field->id }} ? 'Tutup Jadwal' : 'Lihat Jadwal'"></span>
                                <i class="fa-solid transition-transform duration-300" :class="openFieldId === {{ $field->id }} ? 'fa-chevron-up rotate-180' : 'fa-chevron-down'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                   <div x-show="openFieldId === {{ $field->id }}" x-collapse x-cloak>
                        <div class="p-6 bg-gray-50 border-t border-dashed border-gray-200">
                            <template x-if="isLoading">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <template x-for="i in 8">
                                        <div class="animate-pulse bg-gray-200/80 h-24 rounded-2xl"></div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="!isLoading && schedules.length > 0">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4"> <template x-for="slot in schedules" :key="slot.start_time">
                                        <button 
                                            @click="toggleSlot(slot, {{ $field->id }})"
                                            :disabled="slot.is_booked || slot.is_blocked"
                                            class="p-5 rounded-2xl flex flex-col items-center justify-center transition-all duration-200 border-2"
                                            :class="{
                                                'bg-gray-100 text-gray-400 border-gray-100 cursor-not-allowed': slot.is_booked || slot.is_blocked,
                                                'bg-[#0d8173] text-white border-[#0d8173] shadow-md scale-95': isSelected(slot.id, {{ $field->id }}),
                                                'bg-white border-gray-100 hover:border-[#0d8173] text-gray-700': !slot.is_booked && !slot.is_blocked && !isSelected(slot.id, {{ $field->id }})
                                            }"
                                        >
                                            <span class="text-base md:text-lg font-black italic tracking-tight" 
                                                x-text="slot.start_time + ' - ' + slot.end_time"></span>
                                            
                                            <span class="text-xs md:text-sm font-bold opacity-90 mt-1" 
                                                x-text="formatRupiah(slot.price)"></span>
                                            
                                            <template x-if="slot.is_booked">
                                                <span class="text-[10px] uppercase font-black mt-2 bg-red-100 text-red-600 px-2 py-0.5 rounded">Penuh</span>
                                            </template>
                                            <template x-if="slot.is_blocked">
                                                <span class="text-[10px] uppercase font-black mt-2 bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded">Break</span>
                                            </template>
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <template x-if="!isLoading && schedules.length === 0">
                                <div class="text-center py-10 text-gray-400">
                                    <i class="fa-solid fa-calendar-xmark text-4xl mb-2"></i>
                                    <p class="font-bold uppercase italic text-xs">Jadwal Tidak Tersedia</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div x-show="selectedSlots.length > 0" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-10"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-10"
        x-cloak
        class="fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-xl border-t border-gray-100 p-5 z-[60] shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
        
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-[#0d8173]/10 flex items-center justify-center text-[#0d8173] text-xl shadow-inner">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-0.5">Sesi Terpilih</p>
                    <p class="text-sm font-black text-gray-800 flex items-center gap-1.5">
                        <span x-text="totalSesi"></span> Jam Terpilih
                    </p>
                </div>
            </div>
            
            <div class="flex items-center gap-8">
                <div class="text-right">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-0.5">Total Pembayaran</p>
                    <p class="text-2xl font-black text-[#0d8173]" x-text="formatRupiah(totalPayment)"></p>
                </div>
                
                <button :disabled="totalSesi === 0" 
                        @click="prosesCheckout()"
                        class="bg-[#0d8173] text-white px-8 py-3.5 rounded-2xl font-black text-sm uppercase tracking-wide hover:bg-[#0a665b] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-[#0d8173]/20">
                    Lanjut Bayar
                </button>
            </div>
        </div>
    </div>
</main>
@endsection
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    [x-cloak] { display: none !important; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #0d8173; border-radius: 10px; }
</style>

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('bookingSystem', () => ({
            openFieldId: null,
            activeFieldName: '',
            selectedDate: new Date().toISOString().split('T')[0],
            availableDates: [],
            schedules: [],
            selectedSlots: [], 
            isLoading: false,
            fp: null,

            init() {
                this.generateDates();
                this.initDatePicker();
            },

            initDatePicker() {
                this.fp = flatpickr("#datepicker", {
                    disableMobile: "true",
                    dateFormat: "Y-m-d",
                    minDate: "today",
                    onChange: (selectedDates, dateStr) => {
                        this.changeDate(dateStr);
                    }
                });
            },

            generateDates() {
                const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                this.availableDates = [];
                for (let i = 0; i < 7; i++) {
                    const d = new Date();
                    d.setDate(d.getDate() + i);
                    this.availableDates.push({
                        fullDate: d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'),
                        dayName: dayNames[d.getDay()],
                        dayNum: d.getDate(),
                        monthName: monthNames[d.getMonth()]
                    });
                }
            },

            async fetchSchedules() {
                if (!this.openFieldId) return;
                
                this.isLoading = true;
                try {
                    const response = await fetch(`/fields/${this.openFieldId}/schedules?date=${this.selectedDate}`);
                    if (!response.ok) throw new Error("Gagal load data");
                    const data = await response.json();
                    this.schedules = data;
                } catch (error) {
                    console.error("Error fetching schedules:", error);
                    this.schedules = [];
                } finally {
                    this.isLoading = false;
                }
            },

            async toggleJadwal(fieldId, fieldName) {
                if (this.openFieldId === fieldId) {
                    this.openFieldId = null;
                    return;
                }
                this.openFieldId = fieldId;
                this.activeFieldName = fieldName; 
                await this.fetchSchedules();
            },

            async changeDate(date) {
                this.selectedDate = date;
                this.selectedSlots = [];
                if (this.openFieldId) await this.fetchSchedules();
            },

            toggleSlot(slot, fieldId) {
                const uniqueKey = `${fieldId}-${slot.id}`;
                const index = this.selectedSlots.findIndex(s => s.uniqueKey === uniqueKey);

                if (index > -1) {
                    this.selectedSlots.splice(index, 1);
                } else {
                    this.selectedSlots.push({
                        uniqueKey: uniqueKey,
                        fieldId: fieldId,
                        id: slot.id,
                        price: parseInt(slot.price),
                        start_time: slot.start_time,
                        end_time: slot.end_time
                    });
                }
            },

            isSelected(id, fieldId) {
                const uniqueKey = `${fieldId}-${id}`;
                return this.selectedSlots.some(s => s.uniqueKey === uniqueKey);
            },

            // GETTER HITUNG TOTAL SESI secara Real-time
            get totalSesi() {
                return this.selectedSlots.length;
            },

            // GETTER HITUNG TOTAL HARGA secara Real-time
            get totalPayment() {
                return this.selectedSlots.reduce((sum, slot) => sum + slot.price, 0);
            },

            formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { 
                    style: 'currency', 
                    currency: 'IDR', 
                    minimumFractionDigits: 0 
                }).format(number);
            },

            openPicker() {
                if(this.fp) this.fp.open();
            },

            prosesCheckout() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch("{{ route('checkout.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        booking_date: this.selectedDate, 
                        slots: this.selectedSlots 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Alihkan user ke halaman invoice simulasi membawa ID booking-nya
                        window.location.href = `/checkout/invoice/${data.booking_id}`;
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error("Error saat checkout:", error);
                    alert("Terjadi kesalahan, coba lagi.");
                });
            }
        }));
    });
</script>
@endpush