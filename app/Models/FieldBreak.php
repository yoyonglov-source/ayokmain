<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldBreak extends Model
{
    protected $fillable = ['field_id', 'date', 'start_time', 'end_time', 'reason', 'minutes_saved'];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}