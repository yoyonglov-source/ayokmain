<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    // 1. Tampilkan Daftar Staff Milik Owner Ini
    public function index()
    {
        $owner = Auth::user();
        // Mengambil staff yang owner_id-nya adalah id owner saat ini
        $staffs = User::where('owner_id', $owner->id)->where('role', 'staff')->get();

        return view('admin.staff.index', compact('staffs'));
    }

    // 2. Tampilkan Halaman Form Tambah Staff
    public function create()
    {
        return view('admin.staff.create');
    }

    // 3. Proses Simpan Data Staff Baru ke Database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'staff',                 // Otomatis diset sebagai staff
            'owner_id' => Auth::id(),          // Diikat ke ID Owner yang sedang login
            'verification_status' => 'approved' // Otomatis diset approved agar bisa langsung kerja
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff baru berhasil ditambahkan!');
    }

    // 4. Proses Hapus Akun Staff
    public function destroy($id)
    {
        // Pastikan staff yang dihapus memang benar bawahan dari owner yang sedang login
        $staff = User::where('id', $id)->where('owner_id', Auth::id())->firstOrFail();
        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Akun staff berhasil dihapus!');
    }
}