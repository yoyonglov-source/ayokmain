@extends('layouts.user')

@section('content')
<main class="max-w-6xl mx-auto py-10 px-6" x-data="bookingSystem()">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl overflow-hidden border shadow-sm">
                <img src="{{ asset('storage/' . $venue->image) }}" class="w-full h-72 object-cover">
                <div class="p-6">
                    <h1 class="text-3xl font-black text-gray-800 italic uppercase">{{ $venue->name }}</h1>
                    <p class="text-gray-500 text-sm mt-2"><i class="fa fa-map-marker-alt text-brand"></i> {{ $venue->address }}</p>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-black text-gray-800 uppercase italic mb-6 flex items-center gap-3">
                    <span class="w-8 h-8 bg-brand text-white rounded-lg flex items-center justify-center italic text-xs">01</span>
                    Pilih Lapangan
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($venue->fields as $field)
                    <label class="relative border-2 rounded-2xl p-4 cursor-pointer transition-all bg-white shadow-sm"
                           :class="selectedField == {{ $field->id }} ? 'border-brand ring-2 ring-brand/10' : 'border-gray-100'">
                        
                        <input type="radio" name="field_id" value="{{ $field->id }}" class="hidden"
                               @click="selectField({{ json_encode($field) }})">
                        
                        <div class="flex gap-4 items-center">
                            <img src="{{ asset('storage/' . $field->image) }}" class="w-20 h-20 rounded-xl object-cover">
                            <div class="flex-1">
                                <p class="font-black text-gray-800 uppercase italic">{{ $field->name }}</p>
                                <p class="text-brand font-black text-sm">Rp {{ number_format($field->price, 0, ',', '.') }} <span class="text-gray-400 font-normal text-[10px]">/ SESI</span></p>
                            </div>
                            <i class="fa fa-check-circle text-2xl transition" :class="selectedField == {{ $field->id }} ? 'text-brand' : 'text-gray-100'"></i>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100 sticky top-28">
                <h3 class="text-lg font-black text-gray-800 mb-6 uppercase italic text-center border-b pb-4">Jadwal Main</h3>
                
                <div class="mb-6">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Pilih Tanggal</label>
                    <input type="date" class="w-full p-4 bg-gray-50 rounded-xl font-bold text-sm outline-none border-none ring-1 ring-gray-100" value="{{ date('Y-m-d') }}">
                </div>

                <div class="mb-8">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Slot Tersedia</label>
                    
                    <div class="grid grid-cols-3 gap-2" id="slot-container">
                        <template x-for="time in availableSlots" :key="time">
                            <button @click="toggleTime(time)"
                                    :disabled="isBooked(time)"
                                    class="py-3 text-[11px] font-black border-2 rounded-xl transition uppercase italic"
                                    :class="isBooked(time) ? 'bg-gray-100 text-gray-300 border-gray-100 cursor-not-allowed' : 
                                            (selectedTime.includes(time) ? 'bg-brand text-white border-brand' : 'border-gray-50 text-gray-400 hover:border-brand hover:text-brand')">
                                <span x-text="time"></span>
                            </button>
                        </template>
                        
                        <div x-show="!selectedField" class="col-span-3 py-10 text-center text-gray-400 text-xs italic">
                            Silakan pilih lapangan dulu...
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-dashed">
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-[10px] font-black text-gray-400 uppercase">Total (<span x-text="selectedTime.length"></span> Sesi)</span>
                        <span class="font-black text-brand text-2xl italic" x-text="formatRupiah(totalPrice)"></span>
                    </div>
                    <button class="w-full bg-brand text-white py-5 rounded-2xl font-black uppercase tracking-widest text-sm shadow-lg transition-all"
                            :disabled="selectedTime.length == 0"
                            :class="selectedTime.length == 0 ? 'opacity-50 grayscale' : 'hover:scale-[1.02]'">
                        Booking Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function bookingSystem() {
        return {
            selectedField: null,
            fieldPrice: 0,
            bookedSlots: [],
            availableSlots: [],
            selectedTime: [],
            totalPrice: 0,

            selectField(field) {
                this.selectedField = field.id;
                this.fieldPrice = field.price;
                this.selectedTime = []; // Reset pilihan jam
                this.totalPrice = 0;
                
                // Ambil jam dari database field (Misal start 08:00 end 22:00)
                this.generateSlots('08:00', '22:00');
                
                // Masukkan data booking lapangan ini (dari database)
                this.bookedSlots = field.bookings.map(b => b.start_time.substring(0, 5));
            },

            generateSlots(start, end) {
                let slots = [];
                let current = parseInt(start.split(':')[0]);
                let last = parseInt(end.split(':')[0]);
                for (let i = current; i < last; i++) {
                    slots.push((i < 10 ? '0' + i : i) + ':00');
                }
                this.availableSlots = slots;
            },

            isBooked(time) {
                return this.bookedSlots.includes(time);
            },

            toggleTime(time) {
                if (this.selectedTime.includes(time)) {
                    this.selectedTime = this.selectedTime.filter(t => t !== time);
                } else {
                    this.selectedTime.push(time);
                }
                this.totalPrice = this.selectedTime.length * this.fieldPrice;
            },

            formatRupiah(amount) {
                return 'Rp ' + amount.toLocaleString('id-ID');
            }
        }
    }
</script>
@endsection