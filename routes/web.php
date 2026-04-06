<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\FieldController; // Import Controller baru nanti
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FieldBreakController;
use App\Http\Controllers\OperatingHourController;

Route::get('/', function () {
    return view('welcome');
});

// Grup Route yang butuh Login (Auth & Verified)
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // --- MANAJEMEN GEDUNG ---
    Route::resource('venues', VenueController::class);
    Route::post('/field-breaks', [App\Http\Controllers\Admin\FieldBreakController::class, 'store'])->name('field-breaks.store');
    Route::delete('/field-breaks/{id}', [FieldBreakController::class, 'destroy'])->name('field-breaks.destroy');
    Route::put('/operating-hours/{operatingHour}', [OperatingHourController::class, 'update'])->name('operating-hours.update');
    // --- MANAJEMEN LAPANGAN (Nested) ---
    // URL akan menjadi: /venues/{venue}/fields
    Route::prefix('venues/{venue}')->group(function () {
        Route::get('/fields', [FieldController::class, 'index'])->name('venues.fields.index');
        Route::get('/fields/create', [FieldController::class, 'create'])->name('fields.create');
        Route::post('/fields', [FieldController::class, 'store'])->name('fields.store');
        Route::get('/fields/{field}/edit', [FieldController::class, 'edit'])->name('fields.edit');
        Route::put('/fields/{field}', [FieldController::class, 'update'])->name('fields.update');
        Route::delete('/fields/{field}', [FieldController::class, 'destroy'])->name('fields.destroy');
    });

});

// Route Profile Bawaan Laravel Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';