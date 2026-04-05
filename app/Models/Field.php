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
    ];

    /**
     * Relasi Balik ke Venue (Gedung)
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}