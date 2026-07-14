<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Services\IntelligenceService;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    protected $intelligenceService;

    public function __construct(IntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    public function index()
    {
        return view('map.index');
    }

    public function getMapData()
    {
        $user = Auth::user();

        // 1. Shipments
        if ($user->role === 'admin') {
            $shipments = Shipment::whereIn('status', ['Pending', 'Departed', 'In Transit', 'Delayed', 'Redirected'])->get();
        } else {
            $shipments = $user->shipments()->whereIn('status', ['Pending', 'Departed', 'In Transit', 'Delayed', 'Redirected'])->get();
        }

        $liveShipments = [];

        $seaRoutes = [
            'shanghai-rotterdam' => [[31.228, 121.475], [24.5, 119.5], [15.0, 112.0], [1.2, 104.0], [5.0, 98.0], [5.8, 80.0], [12.0, 60.0], [12.0, 45.0], [14.0, 42.5], [20.0, 39.0], [27.0, 34.5], [30.0, 32.5], [31.5, 32.2], [34.0, 25.0], [36.0, 15.0], [37.5, 5.0], [36.0, -5.0], [38.0, -10.0], [45.0, -8.0], [49.0, -4.0], [50.5, 0.0], [51.949, 4.148]],
            'shanghai-hamburg' => [[31.228, 121.475], [24.5, 119.5], [15.0, 112.0], [1.2, 104.0], [5.0, 98.0], [5.8, 80.0], [12.0, 60.0], [12.0, 45.0], [14.0, 42.5], [20.0, 39.0], [27.0, 34.5], [30.0, 32.5], [31.5, 32.2], [34.0, 25.0], [36.0, 15.0], [37.5, 5.0], [36.0, -5.0], [38.0, -10.0], [45.0, -8.0], [49.0, -4.0], [52.0, 3.0], [53.548, 9.987]],
            'shanghai-yokohama' => [[31.228, 121.475], [31.0, 125.0], [30.5, 130.0], [32.0, 134.0], [34.0, 138.5], [35.1, 139.7], [35.443, 139.638]],
            'shanghai-cape town' => [[31.228, 121.475], [24.5, 119.5], [15.0, 112.0], [1.2, 104.0], [5.0, 98.0], [0.0, 90.0], [-15.0, 70.0], [-25.0, 50.0], [-35.0, 35.0], [-36.0, 20.0], [-33.924, 18.424]],
            'shanghai-savannah' => [[31.228, 121.475], [30.0, 130.0], [20.0, 160.0], [15.0, -160.0], [7.5, -81.0], [8.9, -79.5], [9.3, -79.9], [15.0, -75.0], [25.0, -79.0], [28.0, -80.0], [32.08, -81.09]],
            'los angeles-yokohama' => [[33.728, -118.262], [34.0, -130.0], [35.0, -150.0], [36.0, -170.0], [36.0, 170.0], [35.0, 150.0], [35.443, 139.638]],
            'santos-shanghai' => [[-23.953, -46.335], [-30.0, -30.0], [-35.0, -10.0], [-35.5, 19.5], [-30.0, 40.0], [-20.0, 60.0], [-10.0, 80.0], [-6.0, 105.0], [-4.0, 108.0], [-2.0, 108.5], [5.0, 109.0], [15.0, 115.0], [24.0, 119.0], [31.228, 121.475]],
            'jebel ali-hamburg' => [[24.985, 55.027], [26.5, 56.5], [24.0, 59.0], [15.0, 55.0], [12.0, 45.0], [14.0, 42.5], [20.0, 39.0], [27.0, 34.5], [30.0, 32.5], [31.5, 32.2], [34.0, 25.0], [36.0, 15.0], [37.5, 5.0], [36.0, -5.0], [38.0, -10.0], [45.0, -8.0], [49.0, -4.0], [52.0, 3.0], [53.548, 9.987]],
            'new york-cape town' => [[40.678, -73.998], [35.0, -65.0], [20.0, -45.0], [0.0, -25.0], [-15.0, -10.0], [-25.0, 0.0], [-33.924, 18.424]]
        ];

        // Port Coordinates Dictionary
        $portCoords = [
            'shanghai' => [31.228, 121.475],
            'rotterdam' => [51.949, 4.148],
            'los angeles' => [33.728, -118.262],
            'yokohama' => [35.443, 139.638],
            'santos' => [-23.953, -46.335],
            'jebel ali' => [24.985, 55.027],
            'hamburg' => [53.548, 9.987],
            'new york' => [40.678, -73.998],
            'cape town' => [-33.924, 18.424],
            'savannah' => [32.08, -81.09]
        ];

        // If no shipments in DB, generate some dummy ones for presentation
        if ($shipments->isEmpty()) {
            $liveShipments = [];
        } else {
            $liveShipments = $shipments->map(function ($ship) use ($seaRoutes, $portCoords) {
                // Determine origin and current destination
                $originPortName = strtolower(str_replace('Port of ', '', $ship->origin_port));
                $destPortName = strtolower(str_replace('Port of ', '', $ship->destination_port));
                $routeKey = $originPortName . '-' . $destPortName;
                
                // If we have a predefined route for the current destination, use it
                if (isset($seaRoutes[$routeKey])) {
                    $points = $seaRoutes[$routeKey];
                } else {
                    // Fallback: draw straight line from origin to new destination
                    $originCoord = [0, 0];
                    foreach ($portCoords as $p => $c) {
                        if (str_contains($originPortName, $p)) $originCoord = $c;
                    }
                    
                    $destCoord = [0, 0];
                    foreach ($portCoords as $p => $c) {
                        if (str_contains($destPortName, $p)) $destCoord = $c;
                    }
                    
                    if ($destCoord === [0, 0]) {
                        $hash = crc32($destPortName);
                        $destCoord = [($hash % 60) - 30, ($hash % 180) - 90];
                    }
                    $points = [$originCoord, $destCoord];
                }

                $progress = mt_rand(10, 50) / 100;

                return [
                    'id' => $ship->id,
                    'shipment_number' => $ship->shipment_number,
                    'vessel_name' => $ship->vessel_name ?? 'Vessel-' . mt_rand(100, 999),
                    'company' => 'Global Freight Corp',
                    'commodity' => $ship->commodity,
                    'quantity' => $ship->quantity . ' ' . $ship->weight_unit,
                    'origin' => $ship->origin_country . ' - ' . $ship->origin_port,
                    'destination' => $ship->destination_country . ' - ' . $ship->destination_port,
                    'status' => $ship->status,
                    'lat' => $points[0][0],
                    'lng' => $points[0][1],
                    'route' => $points,
                    'progress' => $progress,
                    'speed' => $ship->current_speed ?? mt_rand(10, 25),
                    'heading' => $ship->current_heading ?? mt_rand(0, 360),
                    'profit' => number_format($ship->estimated_profit, 2),
                    'risk_score' => mt_rand(10, 80),
                    'opp_score' => mt_rand(50, 99),
                    'weather' => ['Clear', 'Rain', 'Storm'][mt_rand(0, 2)],
                    'eta' => $ship->estimated_arrival ? $ship->estimated_arrival->format('M d, Y') : 'Unknown',
                    'redirect_url' => route('shipments.redirect', $ship->id)
                ];
            })->toArray();
        }

        // 2. Ports
        $ports = [
            ['name' => 'Port of Singapore', 'country' => 'Singapore', 'lat' => 1.264, 'lng' => 103.840, 'congestion' => 'Low', 'weather' => 'Clear'],
            ['name' => 'Port of Rotterdam', 'country' => 'Netherlands', 'lat' => 51.949, 'lng' => 4.148, 'congestion' => 'High', 'weather' => 'Rain'],
            ['name' => 'Port of Shanghai', 'country' => 'China', 'lat' => 31.228, 'lng' => 121.475, 'congestion' => 'Medium', 'weather' => 'Clear'],
            ['name' => 'Port of Los Angeles', 'country' => 'USA', 'lat' => 33.728, 'lng' => -118.262, 'congestion' => 'High', 'weather' => 'Clear'],
            ['name' => 'Port of Hamburg', 'country' => 'Germany', 'lat' => 53.548, 'lng' => 9.987, 'congestion' => 'Medium', 'weather' => 'Storm'],
            ['name' => 'Jebel Ali Port', 'country' => 'UAE', 'lat' => 24.985, 'lng' => 55.027, 'congestion' => 'Low', 'weather' => 'Clear'],
        ];

        // 3. Global Dashboard Stats
        $stats = [
            'active_shipments' => count($liveShipments),
            'monitored_countries' => 194,
            'current_storms' => 4,
            'high_risk_countries' => 12,
            'best_export' => 'Germany',
            'best_import' => 'Brazil',
            'avg_profit' => '+14.5%',
            'avg_risk' => '32/100'
        ];

        // 4. Country Risk Data (Simulated map matching GeoJSON names)
        $countryRisks = [
            'United States of America' => ['score' => 25, 'level' => 'Low', 'color' => '#22C55E', 'gdp' => '$25T', 'inflation' => '3.1%'],
            'China' => ['score' => 45, 'level' => 'Medium', 'color' => '#F59E0B', 'gdp' => '$17T', 'inflation' => '0.5%'],
            'Russia' => ['score' => 85, 'level' => 'Critical', 'color' => '#EF4444', 'gdp' => '$2.2T', 'inflation' => '7.4%'],
            'Ukraine' => ['score' => 90, 'level' => 'Critical', 'color' => '#EF4444', 'gdp' => '$160B', 'inflation' => '5.1%'],
            'Brazil' => ['score' => 60, 'level' => 'High', 'color' => '#F97316', 'gdp' => '$1.9T', 'inflation' => '4.5%'],
            'Germany' => ['score' => 20, 'level' => 'Low', 'color' => '#22C55E', 'gdp' => '$4.2T', 'inflation' => '2.4%'],
            'Yemen' => ['score' => 95, 'level' => 'Critical', 'color' => '#EF4444', 'gdp' => '$21B', 'inflation' => '20%'],
            // Default logic will handle missing countries
        ];

        // 5. AI Decision Panel Data
        $aiDecisions = $this->intelligenceService->getAIRecommendations();

        return response()->json([
            'shipments' => $liveShipments,
            'ports' => $ports,
            'stats' => $stats,
            'countryRisks' => $countryRisks,
            'aiDecisions' => $aiDecisions
        ]);
    }
}
