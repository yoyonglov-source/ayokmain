<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ImageManager; // 1. Import Trait-nya

class FieldController extends Controller
{
    use ImageManager; // 2. Gunakan Trait di dalam class

    /**
     * Menampilkan daftar lapangan berdasarkan Gedung (Venue)
     */
    public function index($venueId)
    {
        $venue = Venue::with(['fields'])->withCount('fields')
            ->where('user_id', Auth::id())
            ->findOrFail($venueId);

        return view('fields.index', compact('venue'));
    }

    /**
     * Form tambah lapangan
     */
    public function create($venueId)
    {
        $venue = Venue::where('user_id', Auth::id())->findOrFail($venueId);
        return view('fields.create', compact('venue'));
    }

    /**
     * Menyimpan data lapangan baru ke database
     */
    public function store(Request $request, $venueId)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'field_type' => 'required',
            'price_regular' => 'required|numeric',
            'price_peak' => 'required|numeric',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'description' => 'nullable|string',
        ]);

        // 2. Pastikan Gedung milik owner yang sedang login
        $venue = Venue::where('user_id', Auth::id())->findOrFail($venueId);

        // 3. Siapkan data dasar
        $data = $request->all();
        $data['venue_id'] = $venue->id;
        $data['is_active'] = true;

        // 4. Proses Foto menggunakan Trait (Auto WebP & Compress)
        if ($request->hasFile('image')) {
            // Kita panggil fungsi dari Trait, arahkan ke folder 'fields'
            $data['image'] = $this->uploadAndCompress(
                $request->file('image'), 
                'fields', 
                $request->name
            );
        }

        // 5. Simpan ke Database
        Field::create($data);

        return redirect()->route('venues.fields.index', $venue->id)
                         ->with('success', "Lapangan $request->name berhasil ditambahkan!");
    }
}