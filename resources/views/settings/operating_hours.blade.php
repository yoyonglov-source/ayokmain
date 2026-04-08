@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Jam Operasional & Harga</h2>
            <p class="text-sm text-gray-500">Atur jadwal buka-tutup dan periode harga sibuk (peak session).</p>
        </div>
        <div class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold uppercase tracking-wider">
            {{ $venue->name }}
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-500 text-white rounded-xl shadow-sm flex items-center">
            <i class="fa-solid fa-circle-check mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('settings.operating-hours.update') }}" method="POST">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Hari</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Jam Operasional (Buka - Tutup)</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Peak Session (Harga Sibuk)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($operatingHours as $hour)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-700">
                                {{ ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][$hour->day] }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="hours[{{ $hour->id }}][is_closed]" class="sr-only peer" {{ $hour->is_closed ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                                    <span class="ml-2 text-xs font-medium text-gray-400 peer-checked:text-red-500">Libur</span>
                                </label>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <input type="time" name="hours[{{ $hour->id }}][open_time]" value="{{ \Carbon\Carbon::parse($hour->open_time)->format('H:i') }}" class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    <span class="text-gray-400">-</span>
                                    <input type="time" name="hours[{{ $hour->id }}][close_time]" value="{{ \Carbon\Carbon::parse($hour->close_time)->format('H:i') }}" class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="bg-amber-50 p-3 rounded-2xl border border-amber-100 space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="time" name="hours[{{ $hour->id }}][peak_start]" value="{{ \Carbon\Carbon::parse($hour->peak_start)->format('H:i') }}" class="w-full px-3 py-1.5 border border-amber-200 rounded-lg text-xs focus:ring-amber-500 focus:outline-none">
                                        <span class="text-amber-400 text-xs">s/d</span>
                                        <input type="time" name="hours[{{ $hour->id }}][peak_end]" value="{{ \Carbon\Carbon::parse($hour->peak_end)->format('H:i') }}" class="w-full px-3 py-1.5 border border-amber-200 rounded-lg text-xs focus:ring-amber-500 focus:outline-none">
                                    </div>
                                    <p class="text-[10px] text-amber-600 font-medium italic">*Harga Peak berlaku di jam ini</p>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-6 bg-gray-50 flex items-center justify-between border-t border-gray-100">
                <div class="flex items-center space-x-2 text-gray-400 text-xs">
                    <i class="fa-solid fa-circle-info text-emerald-500"></i>
                    <span>Perubahan akan langsung berdampak pada kalkulasi harga booking.</span>
                </div>
                <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-200 transition transform active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 p-6 bg-emerald-900 rounded-3xl text-white flex items-center justify-between overflow-hidden relative group">
        <div class="relative z-10">
            <h4 class="text-lg font-bold">{{ $venue->name }}</h4>
            <p class="text-emerald-300 text-xs">{{ $venue->address }}, {{ $venue->city }}</p>
        </div>
        <div class="relative z-10 hidden md:block">
            <a href="{{ route('venues.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-xs font-semibold transition">
                Kembali ke Daftar Gedung
            </a>
        </div>
        <i class="fa-solid fa-trophy absolute -right-4 -bottom-4 text-8xl opacity-10 group-hover:rotate-12 transition-transform duration-500"></i>
    </div>
</div>
@endsection