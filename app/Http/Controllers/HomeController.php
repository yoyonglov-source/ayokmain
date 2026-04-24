<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Kita ambil data venue agar bisa tampil di halaman depan
        // Kita pakai with('fields') jika nanti ingin menampilkan harga terendah dari lapangan
        $venues = Venue::all();

        return view('user.home', compact('venues'));
    }

    // Tambahkan fungsi ini agar route venue.detail bisa jalan
    public function show($id)
    {
        // Sementara kita return teks dulu untuk ngetes link-nya jalan atau tidak
        return "Anda sedang melihat detail gedung dengan ID: " . $id;
        
        // Nanti di step berikutnya kita ganti jadi:
        // $venue = Venue::with('fields')->findOrFail($id);
        // return view('user.venue_detail', compact('venue'));
    }
}