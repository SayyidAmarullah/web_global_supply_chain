<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    protected $fillable = [
        'name',
        'country',
        'code',
        'latitude',
        'longitude',
        'congestion',
        'wait_time_hours',
        'weather',
    ];
}
