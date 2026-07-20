<?php

namespace App\Services;

class IntelligenceService
{
    /**
     * Get mocked country intelligence data
     */
    public function getCountryIntelligence($countryCode = 'DE')
    {
        return [
            'name' => 'Germany',
            'code' => 'DE',
            'capital' => 'Berlin',
            'region' => 'Western Europe',
            'population' => '83.2 Million',
            'gdp' => '$4.2 Trillion',
            'gdp_growth' => '+1.8%',
            'inflation' => '2.4%',
            'interest_rate' => '4.25%',
            'currency' => 'EUR',
            'exchange_rate' => '1.09 USD',
            'import_volume' => '$1.4 Trillion',
            'export_volume' => '$1.6 Trillion',
            'trade_balance' => '+$200 Billion',
            'political_stability' => 'High',
            'economic_stability' => 'High',
            'risk_level' => 'Low',
            'opportunity_score' => 94,
            'top_exports' => ['Vehicles', 'Machinery', 'Chemicals', 'Electronics'],
            'top_imports' => ['Machinery', 'Data processing equipment', 'Vehicles', 'Chemicals'],
            'major_partners' => ['United States', 'China', 'France', 'Netherlands'],
        ];
    }

    public function getCommodityIntelligence()
    {
        return [
            // Energy
            ['name' => 'Crude Oil (WTI)', 'price' => 82.40, 'trend' => -1.2, 'unit' => 'USD/bbl', 'risk' => 'Medium', 'country' => 'United States', 'code' => 'us'],
            ['name' => 'Crude Oil (Brent)', 'price' => 86.50, 'trend' => -0.8, 'unit' => 'USD/bbl', 'risk' => 'Medium', 'country' => 'Saudi Arabia', 'code' => 'sa'],
            ['name' => 'Natural Gas', 'price' => 2.85, 'trend' => 1.5, 'unit' => 'USD/MMBtu', 'risk' => 'High', 'country' => 'Russia', 'code' => 'ru'],
            ['name' => 'Coal (Newcastle)', 'price' => 135.20, 'trend' => -0.4, 'unit' => 'USD/mt', 'risk' => 'High', 'country' => 'Australia', 'code' => 'au'],
            ['name' => 'Uranium', 'price' => 88.50, 'trend' => 2.1, 'unit' => 'USD/lb', 'risk' => 'High', 'country' => 'Canada', 'code' => 'ca'],
            ['name' => 'Gasoline (RBOB)', 'price' => 2.75, 'trend' => 1.1, 'unit' => 'USD/gal', 'risk' => 'Medium', 'country' => 'United States', 'code' => 'us'],
            
            // Precious Metals
            ['name' => 'Gold', 'price' => 2410.50, 'trend' => 0.8, 'unit' => 'USD/oz', 'risk' => 'Low', 'country' => 'Switzerland', 'code' => 'ch'],
            ['name' => 'Silver', 'price' => 29.80, 'trend' => 1.2, 'unit' => 'USD/oz', 'risk' => 'Low', 'country' => 'Mexico', 'code' => 'mx'],
            ['name' => 'Platinum', 'price' => 985.40, 'trend' => -1.5, 'unit' => 'USD/oz', 'risk' => 'Medium', 'country' => 'South Africa', 'code' => 'za'],
            ['name' => 'Palladium', 'price' => 1050.20, 'trend' => -2.3, 'unit' => 'USD/oz', 'risk' => 'High', 'country' => 'Russia', 'code' => 'ru'],
            
            // Industrial & Battery Metals
            ['name' => 'Copper', 'price' => 4.12, 'trend' => -0.5, 'unit' => 'USD/lb', 'risk' => 'Medium', 'country' => 'Chile', 'code' => 'cl'],
            ['name' => 'Aluminum', 'price' => 2450.00, 'trend' => 1.8, 'unit' => 'USD/mt', 'risk' => 'Medium', 'country' => 'Canada', 'code' => 'ca'],
            ['name' => 'Iron Ore', 'price' => 118.50, 'trend' => 3.2, 'unit' => 'USD/mt', 'risk' => 'High', 'country' => 'Australia', 'code' => 'au'],
            ['name' => 'Lithium', 'price' => 13500.00, 'trend' => -5.4, 'unit' => 'USD/mt', 'risk' => 'High', 'country' => 'China', 'code' => 'cn'],
            ['name' => 'Nickel', 'price' => 18450.00, 'trend' => 2.5, 'unit' => 'USD/mt', 'risk' => 'High', 'country' => 'Indonesia', 'code' => 'id'],
            ['name' => 'Zinc', 'price' => 2950.00, 'trend' => 0.7, 'unit' => 'USD/mt', 'risk' => 'Medium', 'country' => 'Peru', 'code' => 'pe'],
            ['name' => 'Cobalt', 'price' => 28200.00, 'trend' => -1.8, 'unit' => 'USD/mt', 'risk' => 'High', 'country' => 'Democratic Republic of Congo', 'code' => 'cd'],
            ['name' => 'Steel Rebar', 'price' => 580.00, 'trend' => 0.2, 'unit' => 'USD/mt', 'risk' => 'Low', 'country' => 'China', 'code' => 'cn'],
            
            // Agriculture
            ['name' => 'Wheat', 'price' => 5.60, 'trend' => 2.4, 'unit' => 'USD/bu', 'risk' => 'High', 'country' => 'Ukraine', 'code' => 'ua'],
            ['name' => 'Corn', 'price' => 4.20, 'trend' => 0.5, 'unit' => 'USD/bu', 'risk' => 'Low', 'country' => 'United States', 'code' => 'us'],
            ['name' => 'Soybeans', 'price' => 11.45, 'trend' => -2.1, 'unit' => 'USD/bu', 'risk' => 'Low', 'country' => 'Brazil', 'code' => 'br'],
            ['name' => 'Rice (Rough)', 'price' => 18.50, 'trend' => 1.4, 'unit' => 'USD/cwt', 'risk' => 'Medium', 'country' => 'India', 'code' => 'in'],
            ['name' => 'Oats', 'price' => 3.85, 'trend' => -0.9, 'unit' => 'USD/bu', 'risk' => 'Low', 'country' => 'Canada', 'code' => 'ca'],
            
            // Soft Commodities & Livestock
            ['name' => 'Coffee (Arabica)', 'price' => 2.15, 'trend' => 4.5, 'unit' => 'USD/lb', 'risk' => 'High', 'country' => 'Colombia', 'code' => 'co'],
            ['name' => 'Palm Oil', 'price' => 890.00, 'trend' => -1.5, 'unit' => 'USD/mt', 'risk' => 'Medium', 'country' => 'Indonesia', 'code' => 'id'],
            ['name' => 'Cotton', 'price' => 0.82, 'trend' => -0.2, 'unit' => 'USD/lb', 'risk' => 'Medium', 'country' => 'India', 'code' => 'in'],
            ['name' => 'Sugar (Raw)', 'price' => 22.40, 'trend' => 3.1, 'unit' => 'USd/lb', 'risk' => 'High', 'country' => 'Brazil', 'code' => 'br'],
            ['name' => 'Cocoa', 'price' => 7850.00, 'trend' => 8.5, 'unit' => 'USD/mt', 'risk' => 'High', 'country' => 'Ivory Coast', 'code' => 'ci'],
            ['name' => 'Rubber', 'price' => 165.20, 'trend' => 0.6, 'unit' => 'USd/kg', 'risk' => 'Medium', 'country' => 'Thailand', 'code' => 'th'],
            ['name' => 'Lumber', 'price' => 520.00, 'trend' => -4.2, 'unit' => 'USD/1000 bd ft', 'risk' => 'High', 'country' => 'Canada', 'code' => 'ca'],
            ['name' => 'Live Cattle', 'price' => 185.30, 'trend' => 0.8, 'unit' => 'USd/lb', 'risk' => 'Medium', 'country' => 'United States', 'code' => 'us'],
        ];
    }

    public function getPortIntelligence($perPage = 15, $search = null)
    {
        $query = \App\Models\Port::query();
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
        }
        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function getAllPortMapData()
    {
        return \App\Models\Port::select('id', 'name', 'country', 'latitude', 'longitude', 'congestion', 'wait_time_hours', 'weather')->get()->toArray();
    }

    /**
     * Get global risk calculation
     */
    public function getGlobalRiskScore()
    {
        return [
            'overall' => 42,
            'level' => 'Moderate',
            'breakdown' => [
                'weather' => 65,
                'currency' => 30,
                'political' => 45,
                'port' => 55,
                'economic' => 35
            ]
        ];
    }

    public function getAIRecommendations()
    {
        return [
            [
                'type' => 'Route Optimization',
                'title' => 'Avoid Red Sea Transit',
                'description' => 'Due to increasing geopolitical tensions, rerouting vessels via Cape of Good Hope is recommended for European destinations.',
                'impact' => 'High',
            ],
            [
                'type' => 'Commodity Arbitrage',
                'title' => 'Wheat Export Opportunity',
                'description' => 'Wheat prices in North Africa have surged by 15%. Directing agricultural shipments to Egypt yields a 92% opportunity score.',
                'impact' => 'Medium',
            ],
            [
                'type' => 'Market Warning',
                'title' => 'Lithium Supply Bottleneck',
                'description' => 'Battery-grade lithium processing in China is facing severe delays. AI predicts a short-term 5-8% price surge globally.',
                'impact' => 'High',
            ],
            [
                'type' => 'Commodity Arbitrage',
                'title' => 'Brazil Coffee Surplus',
                'description' => 'Record Arabica yields projected in Brazil this season. AI suggests securing forward contracts now to lock in lower baseline prices.',
                'impact' => 'Low',
            ],
            [
                'type' => 'Strategic Hedging',
                'title' => 'Oil Market Volatility',
                'description' => 'WTI and Brent crude showing massive swings. AI model recommends hedging 30% of upcoming Q4 logistics fuel contracts immediately.',
                'impact' => 'High',
            ]
        ];
    }
}
