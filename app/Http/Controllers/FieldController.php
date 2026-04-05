<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FieldController extends Controller
{
    /**
     * Menampilkan daftar lapangan berdasarkan Gedung (Venue)
     */
    public function index($venueId)
    {
        // 1. Ambil data gedung, pastikan milik user yang login (Keamanan SaaS)
        // Kita gunakan withCount agar bisa menghitung jumlah lapangan otomatis
        $venue = Venue::with(['fields'])->withCount('fields')
            ->where('user_id', Auth::id())
            ->findOrFail($venueId);

        // 2. Tampilkan view index lapangan
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
}