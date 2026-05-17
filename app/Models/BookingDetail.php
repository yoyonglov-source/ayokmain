<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'field_id',
        'start_time',
        'end_time',
        'price',
    ];

    // Relasi balik: Setiap detail jam ini menginduk ke sebuah Booking utama
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Relasi ke Lapangan: Agar kita tahu detail jam ini untuk lapangan yang mana
    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}