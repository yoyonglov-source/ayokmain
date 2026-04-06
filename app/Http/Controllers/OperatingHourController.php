<?php

namespace App\Http\Controllers;

use App\Models\OperatingHour;
use Illuminate\Http\Request;

class OperatingHourController extends Controller
{
    public function update(Request $request, OperatingHour $operatingHour)
    {
        $request->validate([
            'open_time' => 'required',
            'close_time' => 'required',
        ]);

        $operatingHour->update([
            'open_time' => $request->open_time,
            'close_time' => $request->close_time,
            'is_closed' => false, // Otomatis jadi buka kalau jam diupdate
        ]);

        return back()->with('success', 'Jadwal operasional berhasil diperbarui!');
    }
}