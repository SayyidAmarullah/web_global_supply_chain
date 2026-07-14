<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiRecommendation extends Model
{
    protected $fillable = [
        'user_id', 'shipment_id', 'type', 'recommended_country', 'recommended_port', 
        'recommended_commodity', 'estimated_revenue', 'estimated_profit', 'shipping_cost', 
        'risk_score', 'opportunity_score', 'confidence_score', 'reason', 'advantages', 
        'disadvantages', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
