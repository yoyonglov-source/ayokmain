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

        <div class="space-y-6">
            @foreach($venue->fields as $field)
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-6 overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center text-left">
                    <div class="lg:col-span-4">
                        <div class="aspect-video rounded-2xl overflow-hidden shadow-inner">
                            <img src="{{ asset('storage/' . $field->image) }}" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <div class="lg:col-span-8">
                        <h4 class="text-2xl font-black italic uppercase tracking-tighter text-gray-800 mb-4">{{ $field->name }}</h4>
                        <div class="space-y-2 mb-6">
                            <div class="flex items-center gap-3 text-sm font-bold text-gray-500">
                                <i class="fa-solid fa-table-cells text-[#0d8173] w-4"></i>
                                <span>{{ $field->category->name ?? 'Sport' }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm font-bold text-gray-500">
                                <i class="fa-solid fa-layer-group text-[#0d8173] w-4"></i>
                                <span>Lantai: {{ $field->floor_type ?? 'Premium Synthetic' }}</span>
                            </div>
                        </div>

                        <button @click="toggleJadwal({{ $field->id }})" 
                            class="bg-[#991b1b] text-white px-6 py-3 rounded-xl font-black uppercase text-xs tracking-widest flex items-center gap-3 hover:bg-red-900 transition-all shadow-md">
                            <span x-text="openFieldId === {{ $field->id }} ? 'Tutup Jadwal' : '{{ $field->schedules_count ?? '10' }} Jadwal Tersedia'"></span>
                            <i class="fa-solid" :class="openFieldId === {{ $field->id }} ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                    </div>
                </div>

                <div x-show="openFieldId === {{ $field->id }}" 
                     x-collapse x-cloak
                     class="mt-8 pt-8 border-t border-dashed border-gray-100">
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                        <template x-for="slot in schedules" :key="slot.id">
                            <button @click="toggleSlot(slot)"
                                :disabled="slot.is_booked || slot.is_blocked"
                                class="relative p-4 rounded-2xl border-2 transition-all text-center flex flex-col"
                                :class="slot.is_booked || slot.is_blocked ? 'bg-gray-50 border-gray-50 opacity-50 cursor-not-allowed' : 
                                        (isSelected(slot.id) ? 'border-[#0d8173] bg-[#0d8173]/5 ring-1 ring-[#0d8173]' : 'border-gray-50 hover:border-gray-200 bg-white')">
                                <span class="text-[9px] font-black uppercase text-gray-400">60 Menit</span>
                                <span class="text-sm font-black italic mt-1 text-gray-800" x-text="slot.start_time + ' - ' + slot.end_time"></span>
                                <span class="text-[10px] font-bold mt-2" :class="slot.is_booked ? 'text-gray-300' : 'text-[#0d8173]'">
                                    <span x-text="slot.is_booked ? 'Booked' : formatRupiah(slot.price)"></span>
                                </span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div x-show="selectedSlots.length > 0" x-transition x-cloak
         class="fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-xl border-t border-gray-100 p-6 z-[60] shadow-2xl">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4 text-left">
                <div class="bg-[#0d8173]/10 p-3 rounded-2xl"><i class="fa-solid fa-cart-shopping text-[#0d8173]"></i></div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sesi Terpilih</p>
                    <p class="font-black text-gray-800 italic"><span x-text="selectedSlots.length"></span> Jam di <span x-text="activeFieldName"></span></p>
                </div>
            </div>
            <div class="flex items-center gap-8">
                <div class="text-right">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Pembayaran</p>
                    <p class="text-2xl font-black text-[#0d8173] italic" x-text="formatRupiah(totalPrice)"></p>
                </div>
                <button class="bg-[#0d8173] text-white px-10 py-4 rounded-2xl font-black uppercase tracking-widest text-sm shadow-xl shadow-[#0d8173]/30">
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
{{-- Load plugin hanya jika belum ada di layout --}}
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener('alpine:init', () => {
        // Cek agar tidak inisialisasi dua kali jika terjadi hot-reload
        if (window.bookingSystemInitialized) return;
        window.bookingSystemInitialized = true;

        Alpine.data('bookingSystem', () => ({
            openFieldId: null,
            activeFieldName: '',
            selectedDate: new Date().toISOString().split('T')[0],
            availableDates: [],
            schedules: [],
            selectedSlots: [],
            totalPrice: 0,
            fp: null,
            fields: @json($venue->fields->map(fn($f) => ['id' => $f->id, 'name' => $f->name])),

            init() {
                this.generateDates();
                
                // Gunakan timeout agar DOM benar-benar siap (Fix kalender gak bisa diklik)
                setTimeout(() => {
                    this.fp = flatpickr("#datepicker", {
                        dateFormat: "Y-m-d",
                        minDate: "today",
                        disableMobile: true,
                        // Fix agar kalender muncul di atas elemen lain
                        static: true, 
                        monthSelectorType: 'static',
                        onChange: (selectedDates, dateStr) => {
                            this.changeDate(dateStr);
                        }
                    });
                }, 100);
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

            openPicker() {
                if(this.fp) this.fp.open();
            },

            async changeDate(date) {
                this.selectedDate = date;
                this.selectedSlots = [];
                this.totalPrice = 0;
                if (this.openFieldId) await this.fetchSchedules();
            },

            async toggleJadwal(fieldId) {
                if (this.openFieldId === fieldId) {
                    this.openFieldId = null;
                } else {
                    this.openFieldId = fieldId;
                    const field = this.fields.find(f => f.id === fieldId);
                    this.activeFieldName = field ? field.name : '';
                    await this.fetchSchedules();
                }
            },

            async fetchSchedules() {
                this.schedules = [];
                try {
                    const response = await fetch(`/api/fields/${this.openFieldId}/schedules?date=${this.selectedDate}`);
                    if (!response.ok) throw new Error();
                    this.schedules = await response.json();
                } catch (error) {
                    console.error("Gagal ambil jadwal");
                }
            },

            toggleSlot(slot) {
                if (this.isSelected(slot.id)) {
                    this.selectedSlots = this.selectedSlots.filter(s => s.id !== slot.id);
                    this.totalPrice -= parseInt(slot.price);
                } else {
                    this.selectedSlots.push(slot);
                    this.totalPrice += parseInt(slot.price);
                }
            },

            isSelected(id) {
                return this.selectedSlots.some(s => s.id === id);
            },

            formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }
        }));
    });
</script>
@endpush