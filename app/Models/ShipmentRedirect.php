<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentRedirect extends Model
{
    protected $guarded = [];

    protected $casts = [
        'old_estimated_arrival' => 'date',
        'new_estimated_arrival' => 'date',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
