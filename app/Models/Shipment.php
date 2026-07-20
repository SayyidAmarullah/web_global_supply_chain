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

    protected static function booted()
    {
        static::creating(function ($shipment) {
            $shipment->calculateFinancials();
        });

        static::updating(function ($shipment) {
            $shipment->calculateFinancials();
        });
    }

    public function calculateFinancials()
    {
        $intelligenceService = app(\App\Services\IntelligenceService::class);
        $commodities = $intelligenceService->getCommodityIntelligence();
        $price = 100; // default price per unit
        
        foreach ($commodities as $c) {
            if (strcasecmp($c['name'], $this->commodity) === 0) {
                $price = $c['price'];
                break;
            }
        }

        $quantity = (float) ($this->quantity ?? 1);
        $cargoValue = $quantity * $price;
        
        // Normalize quantities if they are massive
        $shippingCost = max(5000, $cargoValue * 0.06);
        $insuranceValue = $cargoValue * 0.015;
        
        $estimatedRevenue = $cargoValue + $shippingCost + $insuranceValue + ($cargoValue * 0.18);
        $estimatedProfit = $estimatedRevenue - ($cargoValue + $shippingCost + $insuranceValue);

        $this->cargo_value = $cargoValue;
        $this->shipping_cost = $shippingCost;
        $this->insurance_value = $insuranceValue;
        $this->estimated_revenue = $estimatedRevenue;
        $this->estimated_profit = $estimatedProfit;
    }

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
