<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\FieldBreak;
use Illuminate\Http\Request;

class FieldBreakController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'field_id'   => 'required|exists:fields,id',
            'start_time' => 'required',
            'end_time'   => 'required', 
            'date'       => 'required|date|after_or_equal:today',
            'reason'     => 'required|string|max:255',
        ]);

        // Proteksi double block
        $isAlreadyBlocked = FieldBreak::where('field_id', $request->field_id)
            ->where('date', $request->date)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })->exists();

        if ($isAlreadyBlocked) {
            return back()->with('error', "Gagal! Jam tersebut sudah masuk dalam daftar blokir.");
        }

        FieldBreak::create([
            'field_id'   => $request->field_id,
            'date'       => $request->date,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'reason'     => $request->reason,
        ]);

        return back()->with('success', "Jadwal berhasil diblokir!");
    }

    public function destroy($id)
    {
        $break = FieldBreak::findOrFail($id);
        $break->delete();

        return back()->with('success', "Jadwal blokir telah dihapus dan dibuka kembali.");
    }
}