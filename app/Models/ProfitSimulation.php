<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitSimulation extends Model
{
    protected $fillable = [
        'user_id', 'name', 'selling_price', 'purchase_cost', 'shipping_cost', 
        'insurance_cost', 'import_tax', 'export_tax', 'exchange_rate', 
        'expected_revenue', 'expected_profit', 'margin_percentage',
        'weather_risk', 'inflation_risk', 'political_risk', 'currency_risk', 'total_risk'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
