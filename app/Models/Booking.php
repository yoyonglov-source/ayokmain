<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'venue_id',
        'field_id',
        'booking_date',
        'start_time',
        'end_time',
        'base_price',
        'app_fee',
        'app_fee_bearer',
        'pg_fee',
        'pg_fee_bearer',
        'payment_method',
        'total_amount',
        'net_profit_owner',
        'status',
    ];

    // Relasi ke Venue agar kita bisa tarik nama gedung di invoice
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    // app/Models/Booking.php

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class);
    }
}