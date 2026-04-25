<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal
     */
    protected $fillable = [
        'venue_id',
        'name',
        'field_type',
        'price_regular',
        'price_peak',
        'image',
        'description',
        'is_active',
        'break_quota_minutes', 
        'last_quota_reset'
    ];

    public function breaks()
    {
        return $this->hasMany(FieldBreak::class);
    }

    /**
     * Relasi Balik ke Venue (Gedung)
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function checkAndResetQuota()
    {
        $now = now();
        
        // Pastikan pembandingnya adalah sesama object Carbon
        $lastReset = $this->last_quota_reset ? \Carbon\Carbon::parse($this->last_quota_reset) : null;

        // Reset jika belum pernah reset ATAU sudah masuk minggu yang baru (Senin)
        if (!$lastReset || $now->startOfWeek()->gt($lastReset)) {
            $this->update([
                'break_quota_minutes' => 120,
                'last_quota_reset' => $now
            ]);
        }
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}