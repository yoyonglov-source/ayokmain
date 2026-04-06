<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FieldBreak;
use Illuminate\Http\Request;

class FieldBreakController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
        'field_id'   => 'required|exists:fields,id',
        'start_time' => 'required',
        'end_time'   => 'required|after:start_time', // <--- KUNCI: Jam selesai harus setelah jam mulai
        'date'       => 'nullable|date|after_or_equal:today', // <--- Tambahan: Jangan blokir tanggal kemarin
        'reason'     => 'nullable|string|max:255',
    ]);

        FieldBreak::create($request->all());

        return back()->with('success', 'Jadwal istirahat lapangan berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $break = FieldBreak::findOrFail($id);
        $break->delete();

        return back()->with('success', 'Jadwal istirahat berhasil dihapus, lapangan aktif kembali!');
    }
}