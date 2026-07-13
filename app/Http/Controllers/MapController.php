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

        // If no shipments in DB, generate some dummy ones for presentation
        if ($shipments->isEmpty()) {
            for ($i = 1; $i <= 5; $i++) {
                $lat = mt_rand(-3000, 5000) / 100;
                $lng = mt_rand(-10000, 10000) / 100;
                
                $originLat = $lat + (mt_rand(-2000, 2000)/100);
                $originLng = $lng - (mt_rand(2000, 5000)/100);
                $destLat = $lat + (mt_rand(-2000, 2000)/100);
                $destLng = $lng + (mt_rand(2000, 5000)/100);

                $liveShipments[] = [
                    'id' => $i,
                    'shipment_number' => 'SHP-MOCK-' . mt_rand(1000, 9999),
                    'vessel_name' => 'Demo Vessel ' . $i,
                    'company' => 'Global Freight Corp',
                    'commodity' => ['Crude Oil', 'Wheat', 'Electronics', 'Textiles', 'Automobiles'][mt_rand(0, 4)],
                    'quantity' => mt_rand(100, 5000) . ' Tons',
                    'origin' => 'Origin Port',
                    'destination' => 'Dest Port',
                    'status' => ['In Transit', 'Redirected'][mt_rand(0, 1)],
                    'lat' => $lat,
                    'lng' => $lng,
                    'route' => [
                        [$originLat, $originLng], // Origin
                        [$lat, $lng], // Current
                        [$destLat, $destLng] // Dest
                    ],
                    'speed' => mt_rand(10, 25),
                    'heading' => mt_rand(0, 360),
                    'profit' => number_format(mt_rand(5000, 50000), 2),
                    'risk_score' => mt_rand(10, 80),
                    'opp_score' => mt_rand(50, 99),
                    'weather' => ['Clear', 'Rain', 'Storm'][mt_rand(0, 2)],
                    'eta' => 'Next Week',
                    'redirect_url' => '#'
                ];
            }
        } else {
            $liveShipments = $shipments->map(function ($ship) {
                $lat = $ship->current_latitude ?? (mt_rand(-3000, 5000) / 100);
                $lng = $ship->current_longitude ?? (mt_rand(-10000, 10000) / 100);
                
                $originLat = $lat + (mt_rand(-2000, 2000)/100);
                $originLng = $lng - (mt_rand(2000, 5000)/100);
                $destLat = $lat + (mt_rand(-2000, 2000)/100);
                $destLng = $lng + (mt_rand(2000, 5000)/100);

                return [
                    'id' => $ship->id,
                    'shipment_number' => $ship->shipment_number,
                    'vessel_name' => $ship->vessel_name ?? 'Vessel-' . mt_rand(100, 999),
                    'company' => 'Global Freight Corp',
                    'commodity' => $ship->commodity,
                    'quantity' => $ship->quantity . ' ' . $ship->weight_unit,
                    'origin' => $ship->origin_country . ' (' . $ship->origin_port . ')',
                    'destination' => $ship->destination_country . ' (' . $ship->destination_port . ')',
                    'status' => $ship->status,
                    'lat' => $lat,
                    'lng' => $lng,
                    'route' => [
                        [$originLat, $originLng], // Origin
                        [$lat, $lng], // Current
                        [$destLat, $destLng] // Dest
                    ],
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
