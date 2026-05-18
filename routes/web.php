<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\Admin\FieldBreakController;
use App\Http\Controllers\OperatingHourController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AuthController;

// AREA ADMIN / OWNER (BUTUH LOGIN)
// ==========================================
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    
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
    Route::resource('venues', VenueController::class)->names([
        'index'   => 'admin.venues.index',
        'create'  => 'admin.venues.create',
        'store'   => 'admin.venues.store',
        'show'    => 'admin.venues.show',
        'edit'    => 'admin.venues.edit',
        'update'  => 'admin.venues.update',
        'destroy' => 'admin.venues.destroy',
    ]);

    Route::post('/field-breaks', [FieldBreakController::class, 'store'])->name('admin.field-breaks.store');
    Route::delete('/field-breaks/{id}', [FieldBreakController::class, 'destroy'])->name('admin.field-breaks.destroy');
    Route::put('/operating-hours/{operatingHour}', [OperatingHourController::class, 'update'])->name('operating-hours.update');

    // --- MANAJEMEN LAPANGAN (Nested) ---
    // Gunakan nama rute yang konsisten 'admin.venues.fields.xxx'
    Route::group(['prefix' => 'venues/{venue}/fields', 'as' => 'admin.venues.fields.'], function () {
        Route::get('/', [FieldController::class, 'index'])->name('index');
        Route::get('/create', [FieldController::class, 'create'])->name('create');
        Route::post('/', [FieldController::class, 'store'])->name('store');
        Route::get('/{field}/edit', [FieldController::class, 'edit'])->name('edit');
        Route::put('/{field}', [FieldController::class, 'update'])->name('update');
        Route::delete('/{field}', [FieldController::class, 'destroy'])->name('destroy');
    });

    // --- LOGIC BOOKING DARI SISI ADMIN ---
    Route::post('/booking/process', [BookingController::class, 'store'])->name('booking.process');
    Route::get('/tes-admin', function() {
        return "Rute Admin Terdeteksi!";
    });
});


// AREA USER (GUEST / END-USER)
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('user.home');
//Route::get('/venue/{id}', [HomeController::class, 'show'])->name('venue.detail'); sementara ganti dulu dgn bawah ini
Route::get('/venue/{id}', [VenueController::class, 'show'])->name('venue.detail');
Route::get('/fields/{fieldId}/schedules', [FieldController::class, 'getSchedules']);

// Route untuk proses OTP dan Registrasi User Baru
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp'])->name('auth.verify-otp');
Route::post('/auth/register-user', [AuthController::class, 'storeUser'])->name('auth.register-user');


//FONNTE
Route::post('/checkout/send-otp', [CheckoutController::class, 'sendOtp'])->name('checkout.send_otp');
Route::post('/checkout/verify-otp', [CheckoutController::class, 'verifyOtp'])->name('checkout.verify_otp');

// ==========================================
// PROFILE & AUTH (BAWAAN BREEZE)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/checkout/store', [CheckoutController::class, 'store'])->name('checkout.store');
    // Route baru untuk halaman invoice simulasi
    Route::post('/checkout/pay/{id}', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::get('/checkout/invoice/{id}', [CheckoutController::class, 'invoice'])->name('checkout.invoice');
});

// WAJIB ADA: Memanggil rute login, register, logout dari file auth.php
require __DIR__.'/auth.php';
