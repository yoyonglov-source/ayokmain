<?php

namespace App\Http\Controllers;

use App\Models\OperatingHour;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        // Untuk tahap awal, kita ambil venue pertama milik user
        // Nanti bisa dikembangkan jika user punya banyak venue (dropdown)
        $venue = Venue::where('user_id', Auth::id())->first();
        
        if (!$venue) {
            return redirect()->route('venues.create')->with('info', 'Buat gedung dulu ya!');
        }

        $operatingHours = OperatingHour::where('venue_id', $venue->id)
                            ->orderBy('day', 'asc')
                            ->get();

        return view('settings.operating_hours', compact('operatingHours', 'venue'));
    }

    public function updateHours(Request $request)
    {
        // Kita looping data yang dikirim dari form
        foreach ($request->hours as $id => $data) {
            $hour = OperatingHour::findOrFail($id);
            
            $hour->update([
                'open_time'  => $data['open_time'],
                'close_time' => $data['close_time'],
                'peak_start' => $data['peak_start'],
                'peak_end'   => $data['peak_end'],
                'is_closed'  => isset($data['is_closed']),
            ]);
        }

        return back()->with('success', 'Jadwal operasional dan harga dinamis berhasil diperbarui!');
    }
}