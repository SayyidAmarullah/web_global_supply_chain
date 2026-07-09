<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'departure_date' => 'date',
        'estimated_arrival' => 'date',
        'actual_arrival' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function redirects()
    {
        return $this->hasMany(ShipmentRedirect::class)->orderBy('created_at', 'desc');
    }

    public function activities()
    {
        return $this->hasMany(ShipmentActivity::class)->orderBy('created_at', 'desc');
    }
}
