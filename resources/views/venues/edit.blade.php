<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit: {{ $venue->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('venues.update', $venue->id) }}" method="POST">
                    @csrf
                    @method('PATCH') <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" value="Nama Gedung" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ $venue->name }}" required />
                        </div>
                        <div>
                            <x-input-label for="category" value="Kategori" />
                            <select name="category" class="border-gray-300 focus:border-indigo-500 rounded-md block mt-1 w-full">
                                <option value="Badminton" {{ $venue->category == 'Badminton' ? 'selected' : '' }}>Badminton</option>
                                <option value="Padel" {{ $venue->category == 'Padel' ? 'selected' : '' }}>Padel</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-primary-button>Update Gedung</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>