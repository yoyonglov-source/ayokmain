<?php

namespace App\Http\Controllers;

use App\Models\OperatingHour;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        
        // 1. Ambil SEMUA gedung milik user untuk dropdown
        $allVenues = Venue::where('user_id', $userId)->get();
        
        if ($allVenues->isEmpty()) {
            return redirect()->route('admin.venues.create')->with('info', 'Buat gedung dulu ya!');
        }

        // 2. Cek apakah ada perpindahan gedung via dropdown (?venue_id=X)
        $venueId = $request->query('venue_id');
        if ($venueId) {
            $venue = Venue::where('user_id', $userId)->where('id', $venueId)->firstOrFail();
        } else {
            $venue = $allVenues->first(); // Default gedung pertama
        }

        $operatingHours = OperatingHour::where('venue_id', $venue->id)
                            ->orderBy('day', 'asc')
                            ->get();

        // Sekarang $allVenues sudah dikirim, Blade tidak akan error lagi!
        return view('settings.operating_hours', compact('operatingHours', 'venue', 'allVenues'));
    }

    public function updateHours(Request $request)
    {
        // 1. Update jam operasional gedung yang sedang aktif
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

        // 2. FITUR BULK UPDATE: Jika owner centang "Terapkan ke semua"
        if ($request->has('apply_to_all') && $request->venue_id) {
            $otherVenues = Venue::where('user_id', Auth::id())
                                ->where('id', '!=', $request->venue_id)
                                ->get();

            foreach ($otherVenues as $v) {
                foreach ($request->hours as $id => $data) {
                    $sourceHour = OperatingHour::find($id);
                    if ($sourceHour) {
                        // Cari hari yang sama di gedung lain lalu update
                        OperatingHour::where('venue_id', $v->id)
                                    ->where('day', $sourceHour->day)
                                    ->update([
                                        'open_time'  => $data['open_time'],
                                        'close_time' => $data['close_time'],
                                        'peak_start' => $data['peak_start'],
                                        'peak_end'   => $data['peak_end'],
                                        'is_closed'  => isset($data['is_closed']),
                                    ]);
                    }
                }
            }
        }

        return back()->with('success', 'Jadwal operasional berhasil diperbarui!');
    }

    public function paymentSchema()
    {
        $venue = Venue::where('user_id', Auth::id())->first();
        if (!$venue) {
            return redirect()->route('admin.venues.index')->with('error', 'Silakan buat data gedung terlebih dahulu.');
        }
        return view('settings.payment-schema', compact('venue'));
    }

    public function updatePaymentSchema(Request $request)
    {
        $venue = Venue::where('user_id', Auth::id())->firstOrFail();
        $request->validate([
            'fee_mode' => 'required|in:addon,deduct',
            'pg_fee_bearer' => 'required|in:customer,owner',
        ]);

        $venue->update([
            'fee_mode' => $request->fee_mode,
            'pg_fee_bearer' => $request->pg_fee_bearer,
        ]);

        return redirect()->back()->with('success', 'Skema pembayaran berhasil diperbarui!');
    }
}