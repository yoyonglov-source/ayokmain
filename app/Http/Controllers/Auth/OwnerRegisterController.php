<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OwnerRegisterController extends Controller
{
    // Menampilkan halaman form pendaftaran owner
    public function create()
    {
        return view('auth.register_owner');
    }

    // Memproses data pendaftaran, KYC, dan gedung awal
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'ktp_number' => 'required|string|size:16',
            'ktp_photo' => 'required|image|mimes:jpeg,png,jpg|max:4096', // Max 4MB karena jepretan kamera HP biasanya besar
            'selfie_photo' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            'venue_name' => 'required|string|max:255',
            'google_maps_url' => 'required|url',
            'phone_number' => 'required|string|max:20',
        ]);

        // Gunakan Database Transaction agar jika salah satu upload gagal, data tidak setengah tersimpan
        DB::beginTransaction();

        try {
            // 1. Handle Upload Foto KTP
            $ktpPath = null;
            if ($request->hasFile('ktp_photo')) {
                $ktpExt = $request->file('ktp_photo')->getClientOriginalExtension();
                $ktpName = 'ktp_' . time() . '_' . Str::random(5) . '.' . $ktpExt;
                // Simpan langsung ke folder uploads/kyc di dalam disk public
                $request->file('ktp_photo')->storeAs('uploads/kyc', $ktpName, 'public');
                $ktpPath = 'uploads/kyc/' . $ktpName;
            }

            // 2. Handle Upload Foto Selfie
            $selfiePath = null;
            if ($request->hasFile('selfie_photo')) {
                $selfieExt = $request->file('selfie_photo')->getClientOriginalExtension();
                $selfieName = 'selfie_' . time() . '_' . Str::random(5) . '.' . $selfieExt;
                $request->file('selfie_photo')->storeAs('uploads/kyc', $selfieName, 'public');
                $selfiePath = 'uploads/kyc/' . $selfieName;
            }

            // 3. Simpan Data User Baru (Sebagai Owner)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'owner', // Kunci role sebagai owner
                'ktp_number' => $request->ktp_number,
                'ktp_photo' => $ktpPath,
                'phone' => $request->phone_number,
                'selfie_photo' => $selfiePath,
                'verification_status' => 'pending', // Taktik pengamanan: wajib nunggu verifikasi Yoyon
                'role' => 'owner', 
                'ktp_number' => $request->ktp_number,
                'ktp_photo' => $ktpPath,
                'selfie_photo' => $selfiePath,
            ]);

            // 4. Simpan Data Gedung/Venue Awal Milik Owner Tersebut
            Venue::create([
                'user_id' => $user->id,
                'name' => $request->venue_name,
                'slug' => Str::slug($request->venue_name) . '-' . Str::random(4),
                'category' => '-',
                'address' => '-',
                'google_maps_url' => $request->google_maps_url,
                'city' => '-',
                'phone_number' => $request->phone_number,
                'is_active' => false, // Nonaktifkan dulu agar tidak muncul di halaman depan user penonton
            ]);

            DB::commit();

            // Alihkan ke halaman informasi bahwa verifikasi sedang diproses
            return redirect()->route('register.owner.pending');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem saat mendaftar: ' . $e->getMessage()])->withInput();
        }
    }

    // Menampilkan halaman tunggu verifikasi
    public function pending()
    {
        return view('auth.register_owner_pending');
    }
}