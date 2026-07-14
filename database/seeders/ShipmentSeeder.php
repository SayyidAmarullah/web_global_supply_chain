<?php

namespace Database\Seeders;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        $routes = [
            ['origin' => ['China', 'Port of Shanghai'], 'dest' => ['Netherlands', 'Port of Rotterdam']],
            ['origin' => ['China', 'Port of Shanghai'], 'dest' => ['Germany', 'Port of Hamburg']],
            ['origin' => ['China', 'Port of Shanghai'], 'dest' => ['Japan', 'Port of Yokohama']],
            ['origin' => ['China', 'Port of Shanghai'], 'dest' => ['South Africa', 'Port of Cape Town']],
            ['origin' => ['United States', 'Port of Los Angeles'], 'dest' => ['Japan', 'Port of Yokohama']],
            ['origin' => ['Brazil', 'Port of Santos'], 'dest' => ['China', 'Port of Shanghai']],
            ['origin' => ['UAE', 'Port of Jebel Ali'], 'dest' => ['Germany', 'Port of Hamburg']],
            ['origin' => ['United States', 'Port of New York'], 'dest' => ['South Africa', 'Port of Cape Town']]
        ];

        $commodities = ['Electronics', 'Automotive Parts', 'Crude Oil', 'Agricultural', 'Machinery', 'Textiles'];

        foreach (range(1, 15) as $i) {
            $route = $routes[array_rand($routes)];
            
            Shipment::create([
                'user_id' => $user->id,
                'shipment_number' => 'SHP-' . strtoupper(Str::random(6)),
                'type' => ['import', 'export'][rand(0, 1)],
                'commodity' => $commodities[array_rand($commodities)],
                'quantity' => rand(100, 1000),
                'unit' => 'Tons',
                'container_type' => '40ft Standard',
                'origin_country' => $route['origin'][0],
                'origin_port' => $route['origin'][1],
                'destination_country' => $route['dest'][0],
                'destination_port' => $route['dest'][1],
                'status' => 'In Transit',
                'estimated_arrival' => now()->addDays(rand(5, 30)),
                'shipping_cost' => rand(5000, 20000),
                'estimated_profit' => rand(15000, 50000),
                'current_speed' => rand(12, 24),
                'current_heading' => rand(0, 360),
            ]);
        }
    }
}
