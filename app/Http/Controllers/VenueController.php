<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\ImageManager; // 1. Import Trait

class VenueController extends Controller
{
    use ImageManager; // 2. Gunakan Trait

    public function index()
    {
        $venues = Auth::user()->venues()->withCount('fields')->get(); 
        return view('venues.index', compact('venues'));
    }

    public function create()
    {
        return view('venues.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required',
            'address' => 'required',
            'city' => 'required',
            'phone_number' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', 
        ], [
            'image.required' => 'Wajib mengunggah foto gedung agar tampilan menarik!',
            'image.max' => 'Ukuran foto maksimal adalah 2MB.',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($request->name) . '-' . rand(100, 999);
        $data['city'] = ucwords(strtolower($request->city));

        // Logika Upload Image (Disesuaikan menggunakan Trait)
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadAndCompress(
                $request->file('image'), 
                'venues', 
                $request->name
            );
        }

        Venue::create($data);

        return redirect()->route('venues.index')->with('success', 'Gedung berhasil didaftarkan!');
    }

    public function edit(Venue $venue)
    {
        if ($venue->user_id !== Auth::id()) { abort(403); }
        return view('venues.edit', compact('venue'));
    }

    public function update(Request $request, Venue $venue)
    {
        if ($venue->user_id !== Auth::id()) { abort(403); }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required',
            'address' => 'required',
            'city' => 'required',
            'phone_number' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();
        $data['city'] = ucwords(strtolower($request->city));

        if ($request->hasFile('image')) {
            if ($venue->image) {
                Storage::disk('public')->delete($venue->image);
            }

            // Logika Upload Image Baru (Disesuaikan menggunakan Trait)
            $data['image'] = $this->uploadAndCompress(
                $request->file('image'), 
                'venues', 
                $request->name
            );
        }

        $venue->update($data);

        return redirect()->route('venues.index')->with('success', 'Data gedung berhasil diperbarui!');
    }

    public function destroy(Venue $venue)
    {
        if ($venue->user_id !== Auth::id()) { abort(403); }
        
        if ($venue->image) {
            Storage::disk('public')->delete($venue->image);
        }

        $venue->delete();

        return redirect()->route('venues.index')->with('success', 'Gedung telah dihapus.');
    }
}