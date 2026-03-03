<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id', 'name', 'field_type', 'price_per_hour', 'is_available'
    ];

    // Relasi: Satu Lapangan (Field) adalah milik satu Venue
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}