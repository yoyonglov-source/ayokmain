<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mulai dengan query dasar
        $query = \App\Models\Venue::query();

        // 2. Filter berdasarkan Nama Venue (Jika ada input search)
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 3. Filter berdasarkan Kota
        if ($request->has('city') && $request->city != '') {
            $query->where('city', $request->city);
        }

        // 4. Filter berdasarkan Cabor (Sekarang pakai kolom category)
        if ($request->has('type') && $request->type != '') {
            $query->where('category', $request->type);
        }

        // 5. Eksekusi query
        $venues = $query->get();

        return view('user.home', compact('venues'));
    }

    // app/Http/Controllers/HomeController.php
    public function show($id)
    {
        // Ambil venue, fields, dan bookings yang aktif untuk hari ini
        $venue = \App\Models\Venue::with(['fields.bookings' => function($query) {
            $query->whereDate('booking_date', date('Y-m-d'));
        }])->findOrFail($id);

        return view('user.venue_detail', compact('venue'));
    }
}