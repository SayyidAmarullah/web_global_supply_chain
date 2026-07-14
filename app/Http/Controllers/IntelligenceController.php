<?php

namespace App\Http\Controllers;

use App\Services\IntelligenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntelligenceController extends Controller
{
    protected $intelligenceService;

    public function __construct(IntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    public function index()
    {
        $country = $this->intelligenceService->getCountryIntelligence();
        $commodities = $this->intelligenceService->getCommodityIntelligence();
        $ports = $this->intelligenceService->getPortIntelligence();
        $risk = $this->intelligenceService->getGlobalRiskScore();
        $recommendations = $this->intelligenceService->getAIRecommendations();

        return view('intelligence.index', compact('country', 'commodities', 'ports', 'risk', 'recommendations'));
    }

    public function countries()
    {
        // Placeholder for dedicated countries view
        return redirect()->route('intelligence.index');
    }

    public function commodities(\Illuminate\Http\Request $request)
    {
        $allCommodities = $this->intelligenceService->getCommodityIntelligence();
        $recommendations = $this->intelligenceService->getAIRecommendations();
        
        $perPage = 12;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        
        $itemsForCurrentPage = array_slice($allCommodities, $offset, $perPage);
        
        $commodities = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            count($allCommodities),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stats = [
            'total' => count($allCommodities),
            'top' => collect($allCommodities)->sortByDesc('trend')->first(),
            'bottom' => collect($allCommodities)->sortBy('trend')->first(),
            'highRiskCount' => collect($allCommodities)->where('risk', 'High')->count(),
        ];

        return view('intelligence.commodities', compact('commodities', 'recommendations', 'stats'));
    }

    public function commodityPrices($commodity)
    {
        $countries = [
            // North America
            ['name' => 'United States', 'code' => 'us'], ['name' => 'Canada', 'code' => 'ca'], ['name' => 'Mexico', 'code' => 'mx'],
            // South America
            ['name' => 'Brazil', 'code' => 'br'], ['name' => 'Argentina', 'code' => 'ar'], ['name' => 'Colombia', 'code' => 'co'], ['name' => 'Chile', 'code' => 'cl'], ['name' => 'Peru', 'code' => 'pe'],
            // Europe
            ['name' => 'Germany', 'code' => 'de'], ['name' => 'United Kingdom', 'code' => 'gb'], ['name' => 'France', 'code' => 'fr'], ['name' => 'Italy', 'code' => 'it'], 
            ['name' => 'Spain', 'code' => 'es'], ['name' => 'Netherlands', 'code' => 'nl'], ['name' => 'Switzerland', 'code' => 'ch'], ['name' => 'Sweden', 'code' => 'se'],
            ['name' => 'Poland', 'code' => 'pl'], ['name' => 'Norway', 'code' => 'no'], ['name' => 'Ukraine', 'code' => 'ua'],
            // Asia
            ['name' => 'China', 'code' => 'cn'], ['name' => 'Japan', 'code' => 'jp'], ['name' => 'India', 'code' => 'in'], ['name' => 'South Korea', 'code' => 'kr'],
            ['name' => 'Indonesia', 'code' => 'id'], ['name' => 'Vietnam', 'code' => 'vn'], ['name' => 'Thailand', 'code' => 'th'], ['name' => 'Malaysia', 'code' => 'my'],
            ['name' => 'Philippines', 'code' => 'ph'], ['name' => 'Singapore', 'code' => 'sg'], ['name' => 'Taiwan', 'code' => 'tw'],
            // Middle East & Russia
            ['name' => 'Saudi Arabia', 'code' => 'sa'], ['name' => 'United Arab Emirates', 'code' => 'ae'], ['name' => 'Turkey', 'code' => 'tr'], 
            ['name' => 'Israel', 'code' => 'il'], ['name' => 'Qatar', 'code' => 'qa'], ['name' => 'Russia', 'code' => 'ru'],
            // Africa
            ['name' => 'South Africa', 'code' => 'za'], ['name' => 'Egypt', 'code' => 'eg'], ['name' => 'Nigeria', 'code' => 'ng'], 
            ['name' => 'Kenya', 'code' => 'ke'], ['name' => 'Morocco', 'code' => 'ma'],
            // Oceania
            ['name' => 'Australia', 'code' => 'au'], ['name' => 'New Zealand', 'code' => 'nz']
        ];

        $commoditiesList = $this->intelligenceService->getCommodityIntelligence();
        $baseCommodity = collect($commoditiesList)->firstWhere('name', urldecode($commodity));
        
        if (!$baseCommodity) {
            return response()->json(['success' => false, 'message' => 'Commodity not found']);
        }

        $basePrice = $baseCommodity['price'];
        $unit = $baseCommodity['unit'];
        
        $localPrices = [];
        
        foreach ($countries as $country) {
            // Randomize price between -8% and +12% based on local market factors
            $variance = rand(-80, 120) / 1000; 
            $localPrice = $basePrice * (1 + $variance);
            
            // Randomize supply trend
            $trend = rand(-30, 30) / 10;
            
            $localPrices[] = [
                'country' => $country['name'],
                'code' => $country['code'],
                'price' => round($localPrice, 2),
                'unit' => $unit,
                'trend' => $trend,
                'status' => $variance > 0.06 ? 'Premium' : ($variance < -0.04 ? 'Discount' : 'Market Rate')
            ];
        }

        // Sort by price descending
        usort($localPrices, function($a, $b) {
            return $b['price'] <=> $a['price'];
        });

        return response()->json([
            'success' => true,
            'commodity' => $baseCommodity['name'],
            'data' => $localPrices
        ]);
    }

    public function ports()
    {
        $ports = $this->intelligenceService->getPortIntelligence(15);
        $mapPorts = $this->intelligenceService->getAllPortMapData();
        $risk = $this->intelligenceService->getGlobalRiskScore();
        
        $totalPorts = \App\Models\Port::count();
        $highCongestionCount = \App\Models\Port::where('congestion', 'High')->count();
        $mediumCongestionCount = \App\Models\Port::where('congestion', 'Medium')->count();
        
        $highCongestionPercent = $totalPorts > 0 ? round(($highCongestionCount / $totalPorts) * 100) : 0;
        $avgWaitTime = $totalPorts > 0 ? round(\App\Models\Port::avg('wait_time_hours')) : 0;
        
        // AI Port Risk Analysis calculations
        $stressLevel = $totalPorts > 0 ? round((($highCongestionCount * 100) + ($mediumCongestionCount * 50)) / $totalPorts) : 0;
        
        $worstPorts = \App\Models\Port::where('congestion', 'High')->orderBy('wait_time_hours', 'desc')->limit(2)->get();
        $bestPorts = \App\Models\Port::where('congestion', 'Low')->where('wait_time_hours', '>', 0)->orderBy('wait_time_hours', 'asc')->limit(2)->get();
        
        return view('intelligence.ports', compact(
            'ports', 'mapPorts', 'risk', 'totalPorts', 'highCongestionPercent', 'avgWaitTime',
            'stressLevel', 'worstPorts', 'bestPorts'
        ));
    }

    public function deepAnalysis()
    {
        // Select 100 ports to scan: First 15 (so user sees changes on page 1) + 85 random ports
        $first15 = \App\Models\Port::orderBy('id')->limit(15)->get();
        $random85 = \App\Models\Port::whereNotIn('id', $first15->pluck('id'))->inRandomOrder()->limit(85)->get();
        
        $portsToScan = $first15->merge($random85);
        
        if ($portsToScan->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No ports available to scan.']);
        }

        $lats = $portsToScan->pluck('latitude')->implode(',');
        $lngs = $portsToScan->pluck('longitude')->implode(',');

        try {
            // Call Open-Meteo API for real-time weather at these coordinates
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $lats,
                'longitude' => $lngs,
                'current_weather' => true
            ]);

            if ($response->successful()) {
                $weatherData = $response->json();
                
                // If multiple coordinates are requested, Open-Meteo returns an array of responses
                // If only one, it returns a single object. We'll handle both.
                $results = isset($weatherData['latitude']) ? [$weatherData] : $weatherData;

                foreach ($portsToScan as $index => $port) {
                    if (isset($results[$index]['current_weather'])) {
                        $current = $results[$index]['current_weather'];
                        $windSpeed = $current['windspeed'] ?? 0;
                        $weatherCode = $current['weathercode'] ?? 0;
                        
                        // Map WMO Weather codes to our simple categories
                        // 0-3: Clear/Cloudy, 51-67: Rain, 71-77: Snow, 95-99: Storm
                        $weatherCat = 'Clear';
                        if ($weatherCode >= 51 && $weatherCode <= 67) $weatherCat = 'Rain';
                        if ($weatherCode >= 71 && $weatherCode <= 77) $weatherCat = 'Snow';
                        if ($weatherCode >= 95) $weatherCat = 'Storm';
                        
                        // Determine congestion and wait time based on wind and weather
                        if ($windSpeed > 35 || $weatherCat == 'Storm') {
                            $congestion = 'High';
                            $waitTime = rand(36, 72);
                        } elseif ($windSpeed > 20 || $weatherCat == 'Rain' || $weatherCat == 'Snow') {
                            $congestion = 'Medium';
                            $waitTime = rand(12, 35);
                        } else {
                            $congestion = 'Low';
                            $waitTime = rand(1, 11);
                        }
                        
                        // Update DB
                        $port->update([
                            'weather' => $weatherCat,
                            'congestion' => $congestion,
                            'wait_time_hours' => $waitTime
                        ]);
                    }
                }
                return response()->json(['success' => true]);
            }
        } catch (\Exception $e) {
            // Silently fail if external API is unreachable during demo
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        
        return response()->json(['success' => false, 'message' => 'API Error']);
    }
}
