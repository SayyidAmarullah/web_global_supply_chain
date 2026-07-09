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

    /**
     * Get mocked commodity intelligence data
     */
    public function getCommodityIntelligence()
    {
        return [
            ['name' => 'Crude Oil', 'price' => 82.40, 'trend' => -1.2, 'unit' => 'USD/bbl', 'risk' => 'Medium'],
            ['name' => 'Gold', 'price' => 2410.50, 'trend' => 0.8, 'unit' => 'USD/oz', 'risk' => 'Low'],
            ['name' => 'Wheat', 'price' => 5.60, 'trend' => 2.4, 'unit' => 'USD/bu', 'risk' => 'High'],
            ['name' => 'Copper', 'price' => 4.12, 'trend' => -0.5, 'unit' => 'USD/lb', 'risk' => 'Medium'],
            ['name' => 'Natural Gas', 'price' => 2.85, 'trend' => 1.5, 'unit' => 'USD/MMBtu', 'risk' => 'High'],
        ];
    }

    /**
     * Get mocked port intelligence data
     */
    public function getPortIntelligence()
    {
        return [
            ['name' => 'Port of Rotterdam', 'country' => 'Netherlands', 'congestion' => 'High', 'wait_time' => '48 hours', 'weather' => 'Clear'],
            ['name' => 'Port of Shanghai', 'country' => 'China', 'congestion' => 'Medium', 'wait_time' => '12 hours', 'weather' => 'Rain'],
            ['name' => 'Port of Singapore', 'country' => 'Singapore', 'congestion' => 'Low', 'wait_time' => '4 hours', 'weather' => 'Storm'],
            ['name' => 'Port of Los Angeles', 'country' => 'United States', 'congestion' => 'High', 'wait_time' => '72 hours', 'weather' => 'Clear'],
            ['name' => 'Port of Hamburg', 'country' => 'Germany', 'congestion' => 'Medium', 'wait_time' => '18 hours', 'weather' => 'Cloudy'],
        ];
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

    /**
     * Get AI recommendations
     */
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
            ]
        ];
    }
}
