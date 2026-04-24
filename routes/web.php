<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\Admin\FieldBreakController;
use App\Http\Controllers\OperatingHourController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// ==========================================
// AREA USER (GUEST / END-USER)
// ==========================================
Route::get('/', function () {
    // Sementara kita buat teks polos dulu untuk membuktikan ini halaman USER
    return "<h1>Halaman Depan AyokMain (User Side)</h1><p>Segera Hadir: Pilih Kota & GOR</p>";
    
    // Nanti kalau sudah ada view-nya, kita ganti jadi:
    // return view('user.home'); 
})->name('user.home');

// Simulasi Checkout tetap bisa diakses (untuk sementara)
Route::get('/checkout/{booking_id}', [BookingController::class, 'checkout'])->name('checkout.show');


// ==========================================
// AREA ADMIN / OWNER (BUTUH LOGIN)
// ==========================================
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    
    // Redirect localhost:8000/admin langsung ke dashboard
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    // --- PENGATURAN (Settings) ---
    Route::prefix('settings')->group(function () {
        Route::get('/operating-hours', [SettingController::class, 'index'])->name('settings.operating-hours');
        Route::post('/operating-hours/update', [SettingController::class, 'updateHours'])->name('settings.operating-hours.update');
        Route::get('/payment-schema', [SettingController::class, 'paymentSchema'])->name('settings.payment-schema');
        Route::post('/payment-schema/update', [SettingController::class, 'updatePaymentSchema'])->name('settings.payment-schema.update');
    });

    // --- MANAJEMEN GEDUNG & OPERASIONAL ---
    Route::resource('venues', VenueController::class);
    Route::post('/field-breaks', [FieldBreakController::class, 'store'])->name('admin.field-breaks.store');
    Route::delete('/field-breaks/{id}', [FieldBreakController::class, 'destroy'])->name('admin.field-breaks.destroy');
    Route::put('/operating-hours/{operatingHour}', [OperatingHourController::class, 'update'])->name('operating-hours.update');

    // --- MANAJEMEN LAPANGAN (Nested) ---
    Route::prefix('venues/{venue}')->group(function () {
        Route::get('/fields', [FieldController::class, 'index'])->name('venues.fields.index');
        Route::get('/fields/create', [FieldController::class, 'create'])->name('fields.create');
        Route::post('/fields', [FieldController::class, 'store'])->name('fields.store');
        Route::get('/fields/{field}/edit', [FieldController::class, 'edit'])->name('fields.edit');
        Route::put('/fields/{field}', [FieldController::class, 'update'])->name('fields.update');
        Route::delete('/fields/{field}', [FieldController::class, 'destroy'])->name('fields.destroy');
    });

    // --- LOGIC BOOKING DARI SISI ADMIN (Jika diperlukan) ---
    Route::post('/booking/process', [BookingController::class, 'store'])->name('booking.process');
});

// user homepage
Route::get('/', [HomeController::class, 'index'])->name('user.home');
Route::get('/venue/{id}', [HomeController::class, 'show'])->name('venue.detail');

// ==========================================
// PROFILE & AUTH (BAWAAN BREEZE)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

