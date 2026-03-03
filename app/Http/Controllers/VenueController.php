<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class VenueController extends Controller
{
    // Menampilkan daftar gedung milik user yang login
    public function index()
    {
        $venues = Auth::user()->venues; 
        return view('venues.index', compact('venues'));
    }

    // Menampilkan form tambah gedung
    public function create()
    {
        return view('venues.create');
    }

    // Proses simpan data ke database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required',
            'address' => 'required',
            'city' => 'required',
            'phone_number' => 'required',
        ]);

        Venue::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . rand(100, 999),
            'category' => $request->category,
            'address' => $request->address,
            'city' => $request->city,
            'phone_number' => $request->phone_number,
        ]);

        return redirect()->route('venues.index')->with('success', 'Gedung berhasil didaftarkan!');
    }

    /**
     * Menampilkan form untuk edit gedung.
     */
    public function edit(Venue $venue)
    {
        // Security: Pastikan hanya pemilik yang bisa edit
        if ($venue->user_id !== Auth::id()) {
            abort(403);
        }
        return view('venues.edit', compact('venue'));
    }

    /**
     * Proses memperbarui data di database.
     */
    public function update(Request $request, Venue $venue)
    {
        if ($venue->user_id !== Auth::id()) { abort(403); }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required',
            'address' => 'required',
            'city' => 'required',
            'phone_number' => 'required',
        ]);

        $venue->update($request->all());

        return redirect()->route('venues.index')->with('success', 'Data gedung berhasil diperbarui!');
    }

    /**
     * Menghapus gedung.
     */
    public function destroy(Venue $venue)
    {
        if ($venue->user_id !== Auth::id()) { abort(403); }
        
        $venue->delete();

        return redirect()->route('venues.index')->with('success', 'Gedung telah dihapus.');
    }
}