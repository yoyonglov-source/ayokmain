<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
        'uuid',
        'status',
    ];

    // 🌟 3. Otomatis generate UUID setiap kali ada data Booking baru dibuat
    protected static function booted()
    {
        static::creating(function ($booking) {
            $booking->uuid = (string) Str::uuid();
        });
    }

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
    // Mengubungkan ke tabel booking_details berdasarkan booking_id
        return $this->hasMany(BookingDetail::class, 'booking_id'); 
    }
}