<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    // Field mana saja yang boleh diisi (Security: Mencegah Mass Assignment Vulnerability)
    protected $fillable = [
        'user_id', 'name', 'slug', 'category', 'address', 
        'city', 'phone_number', 'description', 'image', 'is_active','fee_mode',
        'pg_fee_bearer'
    ];

    // Relasi: Satu Venue punya banyak Lapangan (Fields)
    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    // Relasi: Satu Venue dimiliki oleh satu User (Owner)
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Tambahkan di dalam class Venue
    public function operatingHours()
    {
        return $this->hasMany(OperatingHour::class);
    }

}