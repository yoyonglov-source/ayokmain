@extends('layouts.user')

@section('content')
<!-- BREADCRUMBS -->
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

<!-- MAIN CONTENT -->
<main class="max-w-6xl mx-auto py-10 px-6" x-data="bookingSystem()">
    
    <!-- GRID ATAS: FOTO & DESKRIPSI -->
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
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 h-[320px] flex flex-col">
                <h3 class="text-xs font-black text-gray-400 mb-4 uppercase tracking-widest border-b pb-2">Deskripsi Venue</h3>
                <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
                    <article class="text-gray-600 text-sm leading-relaxed prose prose-sm max-w-none text-left">
                        {!! $venue->description ?? '<p class="italic text-gray-400">Belum ada deskripsi.</p>' !!}
                    </article>
                </div>
            </div>
        </aside>
    </div>

    <!-- GRID BAWAH: ACCORDION LAPANGAN & JADWAL -->
    <div class="mt-12 grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- LEFT: Accordion Lapangan -->
        <div class="lg:col-span-7 space-y-4">
            <h3 class="text-xl font-black text-gray-800 uppercase italic mb-6 flex items-center gap-3">
                <span class="w-8 h-8 bg-[#0d8173] text-white rounded-lg flex items-center justify-center italic text-xs shadow-lg">01</span>
                Pilih Lapangan
            </h3>

            @foreach($venue->fields as $field)
            <div class="border border-gray-100 rounded-3xl overflow-hidden bg-white shadow-sm transition-all"
                 :class="selectedFieldId == {{ $field->id }} ? 'ring-2 ring-[#0d8173] border-transparent' : ''">
                
                <button @click="selectField({{ $field->id }}, '{{ $field->name }}')" 
                        class="w-full flex items-center justify-between p-5 hover:bg-gray-50 transition-all text-left">
                    <div class="flex items-center gap-5">
                        <div class="relative">
                            <img src="{{ asset('storage/' . $field->image) }}" class="w-20 h-20 object-cover rounded-2xl shadow-md">
                            <div x-show="selectedFieldId == {{ $field->id }}" class="absolute -top-2 -right-2 bg-[#0d8173] text-white w-6 h-6 rounded-full flex items-center justify-center text-[10px] shadow-lg">
                                <i class="fa fa-check"></i>
                            </div>
                        </div>
                        <div>
                            <p class="font-black text-gray-800 uppercase italic tracking-tight text-lg">{{ $field->name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Klik untuk atur jam main</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right transition-transform duration-300 text-gray-300" 
                       :class="selectedFieldId == {{ $field->id }} ? 'rotate-90 text-[#0d8173]' : ''"></i>
                </button>

                <div x-show="selectedFieldId == {{ $field->id }}" x-transition.opacity class="p-6 bg-gray-50/50 border-t border-gray-50">
                    <p class="text-xs text-gray-500 leading-relaxed italic text-left">
                        <i class="fa-solid fa-quote-left mr-2 opacity-20"></i>
                        {{ $field->description ?? 'Gunakan lapangan ini untuk performa terbaik permainanmu.' }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- RIGHT: Schedule Grid -->
        <div class="lg:col-span-5">
            <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-50 sticky top-28">
                <div class="text-center mb-8">
                    <h3 class="text-lg font-black text-gray-800 uppercase italic tracking-tighter" x-text="selectedFieldName || 'Jadwal Main'"></h3>
                    <div class="inline-flex items-center gap-3 mt-4 bg-gray-50 p-2 rounded-xl border border-gray-100">
                        <i class="fa-regular fa-calendar text-[#0d8173] ml-2"></i>
                        <input type="date" x-model="selectedDate" @change="fetchSchedules()"
                               class="bg-transparent border-none text-sm font-black focus:ring-0 cursor-pointer">
                    </div>
                </div>

                <!-- Grid Jadwal Sesuai Capture -->
                <div class="grid grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                    <template x-for="slot in schedules" :key="slot.id">
                        <button @click="toggleSlot(slot)"
                                :disabled="slot.is_booked"
                                class="relative p-4 rounded-2xl border-2 transition-all text-left flex flex-col group"
                                :class="slot.is_booked ? 'bg-gray-50 border-gray-50 opacity-60 cursor-not-allowed' : 
                                        (isSelected(slot.id) ? 'border-[#0d8173] bg-[#0d8173]/5 ring-1 ring-[#0d8173]' : 'border-gray-50 hover:border-gray-200 bg-white')">
                            
                            <span class="text-[9px] font-black uppercase tracking-tighter"
                                  :class="slot.is_booked ? 'text-gray-300' : 'text-gray-400'">60 Menit</span>
                            
                            <span class="text-sm font-black italic mt-1"
                                  :class="slot.is_booked ? 'text-gray-300 line-through' : 'text-gray-800'"
                                  x-text="slot.start_time + ' - ' + slot.end_time"></span>

                            <span class="text-xs font-bold mt-2"
                                  :class="slot.is_booked ? 'text-gray-300' : 'text-[#0d8173]'"
                                  x-text="slot.is_booked ? 'Booked' : formatRupiah(slot.price)"></span>
                        </button>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="!selectedFieldId" class="py-12 text-center text-gray-400 italic text-sm">
                    <i class="fa-regular fa-calendar-check text-4xl mb-3 block opacity-10"></i>
                    Silakan pilih lapangan terlebih dahulu
                </div>

                <!-- Checkout Info -->
                <div x-show="selectedSlots.length > 0" class="mt-8 pt-6 border-t border-dashed border-gray-200" x-transition>
                    <div class="flex justify-between items-center mb-6">
                        <div class="text-left">
                            <span class="text-[10px] font-black text-gray-400 uppercase block">Total Bayar</span>
                            <span class="text-xs text-gray-500 italic"><span x-text="selectedSlots.length"></span> Sesi terpilih</span>
                        </div>
                        <span class="text-2xl font-black text-[#0d8173] italic tracking-tighter" x-text="formatRupiah(totalPrice)"></span>
                    </div>
                    <button class="btn-primary-gradient w-full py-4 rounded-2xl font-black uppercase tracking-widest text-sm shadow-xl shadow-[#0d8173]/20">
                        Booking Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #0d8173; border-radius: 10px; }
    
    .btn-primary-gradient {
        background: linear-gradient(135deg, #0d8173 0%, #0a665b 100%);
        color: white;
        transition: all 0.3s ease;
    }
    .btn-primary-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(13, 129, 115, 0.4);
    }
</style>
@endsection

@push('scripts')
<script>
function bookingSystem() {
    return {
        selectedFieldId: null,
        selectedFieldName: '',
        selectedDate: new Date().toISOString().split('T')[0],
        schedules: [],
        selectedSlots: [],
        totalPrice: 0,

        async selectField(id, name) {
            this.selectedFieldId = id;
            this.selectedFieldName = name;
            this.selectedSlots = [];
            this.totalPrice = 0;
            await this.fetchSchedules();
        },

        async fetchSchedules() {
            if (!this.selectedFieldId) return;
            try {
                // Catatan: Pastikan Route API ini sudah Captain buat di routes/api.php
                const response = await fetch(`/api/fields/${this.selectedFieldId}/schedules?date=${this.selectedDate}`);
                this.schedules = await response.json();
            } catch (e) {
                console.error("Gagal mengambil jadwal:", e);
                // Dummy data untuk testing jika API belum siap
                this.schedules = [
                    { id: 1, start_time: '08:00', end_time: '09:00', price: 150000, is_booked: false },
                    { id: 2, start_time: '09:00', end_time: '10:00', price: 150000, is_booked: true },
                ];
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
            return new Intl.NumberFormat('id-ID', { 
                style: 'currency', 
                currency: 'IDR', 
                minimumFractionDigits: 0 
            }).format(number);
        }
    }
}
</script>
@endpush