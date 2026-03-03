<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftarkan Gedung Olahraga Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('venues.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Nama Gedung')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required />
                        </div>

                        <div>
                            <x-input-label for="category" :value="__('Kategori Olahraga')" />
                            <select name="category" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="Badminton">Badminton</option>
                                <option value="Padel">Padel</option>
                                <option value="Futsal">Futsal</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="city" :value="__('Kota')" />
                            <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" required />
                        </div>

                        <div>
                            <x-input-label for="phone_number" :value="__('Nomor HP/WhatsApp')" />
                            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" required />
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="address" :value="__('Alamat Lengkap')" />
                        <textarea name="address" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3" required></textarea>
                    </div>

                    <div class="mt-6">
                        <x-primary-button>
                            {{ __('Simpan Gedung') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>