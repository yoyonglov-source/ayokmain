<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingHour extends Model
{
    protected $fillable = ['venue_id', 'day', 'open_time', 'close_time', 'is_closed'];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}