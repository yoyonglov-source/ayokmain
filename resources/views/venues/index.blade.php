@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">
        Gedung Saya
    </h2>
</div>

<!-- GRID -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- CARD TAMBAH GEDUNG -->
    <a href="{{ route('venues.create') }}"
       class="border-2 border-dashed border-gray-300 rounded-2xl p-6 flex flex-col items-center justify-center text-gray-500 hover:border-emerald-500 hover:text-emerald-600 transition">

        <div class="text-4xl mb-2">+</div>
        <p class="font-semibold">Tambah Gedung</p>
    </a>

    @forelse($venues as $venue)

    <!-- CARD GEDUNG -->
    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden hover:shadow-md transition">

        <!-- IMAGE -->
        <div class="h-40 bg-gray-200">
            @if($venue->image)
                <img src="{{ asset('storage/'.$venue->image) }}"
                     class="w-full h-full object-cover">
            @endif
        </div>

        <!-- CONTENT -->
        <div class="p-4">

            <h3 class="font-bold text-gray-800 text-lg">
                {{ $venue->name }}
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                📍 {{ $venue->city }}
            </p>

            <p class="text-sm text-gray-500 mt-1">
                🏷 {{ $venue->category }}
            </p>

            <!-- ACTION -->
            <div class="flex gap-2 mt-4">

                <a href="{{ route('venues.edit', $venue->id) }}"
                   class="flex-1 text-center bg-gray-100 text-gray-700 py-2 rounded-lg text-sm">
                    Edit
                </a>

                <form action="{{ route('venues.destroy', $venue->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button onclick="return confirm('Hapus gedung ini?')"
                            class="w-full bg-red-100 text-red-600 py-2 rounded-lg text-sm">
                        Hapus
                    </button>
                </form>

            </div>

        </div>

    </div>

    @empty

    <div class="col-span-3 text-center text-gray-400 py-20">
        Belum ada gedung
    </div>

    @endforelse

</div>

@endsection