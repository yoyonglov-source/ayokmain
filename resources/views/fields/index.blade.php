@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2">
                <a href="{{ route('venues.index') }}" class="hover:text-emerald-600 transition-colors">Gedung Saya</a>
                <span class="mx-2">/</span>
                <span class="text-gray-800 font-medium">{{ $venue->name }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Lapangan</h1>
            @if (session('success'))
                <div id="alert-success" class="relative mb-4 p-4 pr-12 bg-emerald-100 text-emerald-700 rounded-2xl border border-emerald-200">
                    <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                    <button onclick="document.getElementById('alert-success').remove()" class="absolute top-4 right-4 text-emerald-500 hover:text-emerald-800 transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div id="alert-error" class="relative mb-4 p-4 pr-12 bg-red-100 text-red-700 rounded-2xl border border-red-200">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> <strong>Waduh!</strong> {{ session('error') }}
                    <button onclick="document.getElementById('alert-error').remove()" class="absolute top-4 right-4 text-red-500 hover:text-red-800 transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
            <p class="text-sm text-gray-500">Atur detail dan harga lapangan di {{ $venue->name }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Lapangan</p>
            <p class="text-xl font-black text-gray-800">{{ $venue->fields_count }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-emerald-500">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aktif</p>
            <p class="text-xl font-black text-emerald-600">{{ $venue->fields()->where('is_active', true)->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <a href="{{ route('fields.create', $venue->id) }}" class="group relative flex flex-col items-center justify-center bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl p-8 transition-all hover:border-emerald-500 hover:bg-emerald-50/30 min-h-[300px]">
            <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 mb-4 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-plus text-emerald-600 text-xl"></i>
            </div>
            <span class="text-sm font-bold text-gray-700">Tambah Lapangan Baru</span>
            <p class="text-[10px] text-gray-400 mt-2 text-center px-4">Klik untuk menambah lapangan pada gedung ini</p>
        </a>

        @foreach($venue->fields as $field)
            <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden hover:shadow-md transition-all group min-h-[300px] flex flex-col relative">
                <div class="relative h-40 bg-gray-200">
                    <img src="{{ $field->image ? asset('storage/' . $field->image) : 'https://via.placeholder.com/400x200?text=No+Photo' }}" 
                         class="w-full h-full object-cover transition-transform group-hover:scale-105 duration-500">
                    <div class="absolute bottom-3 left-3">
                        <span class="bg-white/90 backdrop-blur-sm text-gray-800 text-[10px] font-bold px-2 py-1 rounded-md shadow-sm uppercase">
                            <i class="fa-solid fa-layer-group text-emerald-500 mr-1"></i> {{ $field->field_type }}
                        </span>
                    </div>
                </div>

                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $field->name }}</h3>
                                <div class="flex items-center mt-1">
                                    <div class="w-2 h-2 rounded-full {{ $field->is_active ? 'bg-emerald-500' : 'bg-amber-500' }} mr-2"></div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                                        {{ $field->is_active ? 'Tersedia' : 'Maintenance' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="openFieldModal('breakModal{{ $field->id }}')" class="text-gray-300 hover:text-red-500 transition-colors">
                                    <i class="fa-solid fa-calendar-minus text-sm"></i>
                                </button>
                                <a href="{{ route('fields.edit', [$venue->id, $field->id]) }}" class="text-gray-300 hover:text-amber-500 transition-colors">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 p-3 bg-gray-50 rounded-2xl">
                            <div>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Reguler</p>
                                <p class="text-sm font-black text-gray-700">Rp{{ number_format($field->price_regular, 0, ',', '.') }}</p>
                            </div>
                            <div class="border-l border-gray-200 pl-3">
                                <p class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Peak Time</p>
                                <p class="text-sm font-black text-emerald-700">Rp{{ number_format($field->price_peak, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="breakModal{{ $field->id }}" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeFieldModal('breakModal{{ $field->id }}')"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-t-[2.5rem] sm:rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                        <div class="bg-red-600 px-6 py-5 flex justify-between items-center text-white">
                            <div>
                                <h3 class="font-bold text-lg leading-tight"><i class="fa-solid fa-clock-rotate-left mr-2"></i> Blokir Jadwal</h3>
                                <p class="text-[10px] text-red-100 opacity-80 uppercase tracking-widest font-semibold">{{ $field->name }}</p>
                            </div>
                            <button onclick="closeFieldModal('breakModal{{ $field->id }}')" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-white/20 transition-colors">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </button>
                        </div>
                        <div class="p-5 sm:p-8 bg-white">
                            <form action="{{ route('admin.field-breaks.store') }}" method="POST" class="space-y-5">
                                @csrf
                                <input type="hidden" name="field_id" value="{{ $field->id }}">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 ml-1">Pilih Tanggal</label>
                                        <input type="date" name="date" class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm focus:ring-red-500 focus:border-red-500 py-3.5 px-4">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 ml-1">Mulai</label>
                                            <input type="time" name="start_time" class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm focus:ring-red-500 focus:border-red-500 py-3.5 px-4" required>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 ml-1">Selesai</label>
                                            <input type="time" name="end_time" class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm focus:ring-red-500 focus:border-red-500 py-3.5 px-4" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-2 ml-1">Alasan Penutupan</label>
                                        <input type="text" name="reason" placeholder="Contoh: Maintenance" class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm focus:ring-red-500 focus:border-red-500 py-3.5 px-4">
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-red-600 text-white font-black py-4 rounded-2xl hover:bg-red-700 shadow-lg shadow-red-100">SIMPAN BLOKIR</button>
                            </form>
                            <div class="mt-10 pt-6 border-t border-gray-100">
                                <h4 class="text-[11px] font-black text-gray-800 uppercase tracking-widest mb-4 italic">Jadwal Terblokir Saat Ini:</h4>
                                <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1 custom-scrollbar">
                                    @forelse($field->breaks as $break)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                            <div class="flex items-center gap-4">
                                                <div class="w-11 h-11 rounded-2xl flex items-center justify-center shadow-sm {{ $break->date ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600' }}">
                                                    <i class="fa-solid {{ $break->date ? 'fa-calendar-day' : 'fa-clock' }} text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-black uppercase {{ $break->date ? 'text-amber-600' : 'text-blue-600' }}">{{ $break->date ? \Carbon\Carbon::parse($break->date)->translatedFormat('d M Y') : 'Rutin Harian' }}</p>
                                                    <p class="text-sm font-black text-gray-800">{{ substr($break->start_time, 0, 5) }} - {{ substr($break->end_time, 0, 5) }}</p>
                                                </div>
                                            </div>
                                            <form action="{{ route('admin.field-breaks.destroy', $break->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-gray-300 hover:text-red-500 p-2"><i class="fa-solid fa-trash-can text-xs"></i></button>
                                            </form>
                                        </div>
                                    @empty
                                        <p class="text-[10px] text-gray-400 text-center font-bold uppercase py-4">Kosong</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div> </div> </div> @endforeach
    </div> <div class="mt-12">
        <x-venue-footer-tips />
    </div>

</div>

<script>
    function openFieldModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeFieldModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e2e2; border-radius: 10px; }
</style>
@endsection