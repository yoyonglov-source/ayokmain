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

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}