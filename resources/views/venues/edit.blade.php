@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ imagePreview: '{{ asset('storage/' . $venue->image) }}' }">
    
    <div class="mb-6">
        <a href="{{ route('venues.index') }}" class="inline-flex items-center text-emerald-700 hover:text-emerald-800 font-medium transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Kembali ke Daftar Gedung
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Edit Gedung: {{ $venue->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">Lengkapi data gedung Anda untuk mulai mengelola lapangan.</p>
            </div>

            <form action="{{ route('venues.update', $venue->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-4">Foto Utama Gedung</label>
                        <div class="relative flex flex-col items-center justify-center bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl p-8 transition-all hover:border-emerald-500 group">
                            <template x-if="imagePreview">
                                <div class="w-full">
                                    <img :src="imagePreview" class="w-full h-64 object-cover rounded-2xl shadow-sm mb-4">
                                </div>
                            </template>
                            
                            <label for="image_upload" class="cursor-pointer flex flex-col items-center">
                                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 mb-3 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-camera text-emerald-600"></i>
                                </div>
                                <span class="text-sm font-bold text-gray-700">Ganti Foto Gedung</span>
                                <span class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest text-center">Format: JPG, PNG, WEBP (Maks. 2MB)</span>
                            </label>
                            <input id="image_upload" type="file" name="image" class="hidden" accept="image/*"
                                @change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = (e) => { imagePreview = e.target.result; };
                                        reader.readAsDataURL(file);
                                    }
                                ">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Nama Gedung</label>
                            <input name="name" type="text" placeholder="Contoh: GOR Sejahtera" 
                                   class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-all" 
                                   value="{{ old('name', $venue->name) }}" required />
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Kategori Utama</label>
                            <select name="category" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-all">
                                <option value="Badminton" {{ old('category', $venue->category) == 'Badminton' ? 'selected' : '' }}>Badminton</option>
                                <option value="Padel" {{ old('category', $venue->category) == 'Padel' ? 'selected' : '' }}>Padel</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Kota</label>
                            <input name="city" type="text" placeholder="Pilih Kota"
                                   class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-all" 
                                   value="{{ old('city', $venue->city) }}" required />
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Nomor HP/WhatsApp</label>
                            <input name="phone_number" type="text" placeholder="0812..."
                                   class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-all" 
                                   value="{{ old('phone_number', $venue->phone_number) }}" required />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Alamat Lengkap</label>
                        <textarea name="address" rows="4" placeholder="Tulis alamat detail di sini..."
                                  class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-all">{{ old('address', $venue->address) }}</textarea>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-10 py-4 rounded-xl shadow-lg shadow-emerald-100 transition-all active:scale-95">
                            Simpan Perubahan Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <hr class="my-5">

<div class="d-flex align-items-center mb-4 p-3 bg-white shadow-sm rounded-4 border-start border-success border-4">
    <div class="bg-success-subtle p-2 rounded-circle me-3">
        <i class="bi bi-clock-fill text-success fs-4"></i>
    </div>
    <div>
        <h5 class="mb-0 fw-bold text-dark">Jadwal Operasional</h5>
        <small class="text-muted">Klik "Atur Jam" untuk melakukan perubahan instan</small>
    </div>
</div>

<div class="pb-3" style="overflow-x: auto; white-space: nowrap;">
    <div class="d-flex flex-row gap-3 pb-2">
        @foreach($venue->operatingHours->sortBy('day') as $hour)
        <div class="card border-0 shadow-sm transition-card" 
             style="min-width: 155px; width: 155px; height: 155px; border-radius: 20px; background: #ffffff; border: 1px solid #f1f1f1 !important; flex-shrink: 0;">
            <div class="card-body p-2 d-flex flex-column justify-content-center text-center">
                
                <p class="fw-bold mb-1 text-secondary" style="font-size: 0.8rem; text-uppercase: uppercase;">
                    {{ ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$hour->day] }}
                </p>

                <div id="display-{{ $hour->id }}">
                    <div class="badge {{ $hour->is_closed ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} mb-2 px-2 py-1" style="font-size: 0.65rem;">
                        {{ $hour->is_closed ? 'Tutup' : 'Buka' }}
                    </div>
                    <h6 class="fw-bold mb-2 text-dark" style="font-size: 0.9rem;">
                        {{ $hour->is_closed ? '--:--' : substr($hour->open_time, 0, 5) . '-' . substr($hour->close_time, 0, 5) }}
                    </h6>
                    <button type="button" onclick="enableEdit({{ $hour->id }})" class="btn btn-sm btn-link text-primary p-0 fw-bold text-decoration-none" style="font-size: 0.7rem;">
                        Atur Jam
                    </button>
                </div>

                <form id="form-{{ $hour->id }}" action="{{ route('operating-hours.update', $hour->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('PUT')
                    <input type="time" name="open_time" value="{{ substr($hour->open_time, 0, 5) }}" class="form-control form-control-sm mb-1 p-1 text-center" style="font-size: 0.7rem;" required>
                    <input type="time" name="close_time" value="{{ substr($hour->close_time, 0, 5) }}" class="form-control form-control-sm mb-1 p-1 text-center" style="font-size: 0.7rem;" required>
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-success btn-sm w-100 py-0" style="font-size: 0.6rem;">Simpan</button>
                        <button type="button" onclick="cancelEdit({{ $hour->id }})" class="btn btn-light border btn-sm w-100 py-0" style="font-size: 0.6rem;">Batal</button>
                    </div>
                </form>

            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    /* Mencegah card turun ke bawah */
    .d-flex.flex-row {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important; /* KUNCI: Mencegah pindah baris */
    }

    .transition-card {
        transition: all 0.3s ease;
    }
    .transition-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        border-color: #198754 !important;
    }
    .bg-success-subtle { background-color: #e6fcf5; }
    .bg-danger-subtle { background-color: #fff5f5; }
</style>

<script>
    function enableEdit(id) {
        document.getElementById('display-' + id).style.display = 'none';
        document.getElementById('form-' + id).style.display = 'block';
    }
    function cancelEdit(id) {
        document.getElementById('display-' + id).style.display = 'block';
        document.getElementById('form-' + id).style.display = 'none';
    }
</script>

    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start space-x-4">
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-image text-amber-600"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 text-sm">Foto Menarik</h4>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed">Gunakan foto berkualitas tinggi untuk meningkatkan minat pelanggan hingga 40%.</p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start space-x-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-tags text-emerald-600"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 text-sm">Harga Kompetitif</h4>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed">Atur tarif yang sesuai dengan harga pasar lokal untuk menarik lebih banyak pelanggan.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start space-x-4">
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-clock text-purple-600"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 text-sm">Jadwal Operasional</h4>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed">Tetapkan jam buka dan tutup yang akurat agar booking lancar dan pelanggan puas.</p>
            </div>
        </div>
    </div>
</div>
@endsection