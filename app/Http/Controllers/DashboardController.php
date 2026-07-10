<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Services\IntelligenceService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $intelligenceService;

    public function __construct(IntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    public function index()
    {
        $user = Auth::user();

        // Get basic stats
        if ($user->role === 'admin') {
            $totalShipments = Shipment::count();
            $activeTransit = Shipment::where('status', 'In Transit')->count();
            $redirected = Shipment::where('status', 'Redirected')->count();
            $totalProfit = Shipment::sum('estimated_profit');
        } else {
            $totalShipments = $user->shipments()->count();
            $activeTransit = $user->shipments()->where('status', 'In Transit')->count();
            $redirected = $user->shipments()->where('status', 'Redirected')->count();
            $totalProfit = $user->shipments()->sum('estimated_profit');
        }

        // Mock chart data for financial trends
        $chartMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
        $chartProfits = [120000, 145000, 130000, 185000, 210000, 195000, $totalProfit > 0 ? $totalProfit : 250000];
        $chartRisks = [65, 50, 45, 75, 40, 30, 25];

        // Fetch Global Intelligence Data for Dashboard Widgets
        $riskData = $this->intelligenceService->getGlobalRiskScore();
        $commodities = $this->intelligenceService->getCommodityIntelligence();
        $country = $this->intelligenceService->getCountryIntelligence();
        $aiRecommendations = $this->intelligenceService->getAIRecommendations();
        
        // Mock Currency Data
        $currencies = [
            ['pair' => 'USD/EUR', 'rate' => '0.92', 'trend' => '+0.45%', 'status' => 'success'],
            ['pair' => 'USD/JPY', 'rate' => '155.40', 'trend' => '-1.20%', 'status' => 'danger'],
            ['pair' => 'USD/GBP', 'rate' => '0.78', 'trend' => '+0.10%', 'status' => 'success']
        ];

        // Mock News Data
        $news = [
            ['title' => 'Suez Canal transit risks elevated amid new geopolitical tensions.', 'category' => 'Risk Alert', 'time' => '2 hours ago'],
            ['title' => 'Global Copper prices drop by 2.4% following China economic data.', 'category' => 'Market', 'time' => '5 hours ago'],
            ['title' => 'Port of Rotterdam experiences severe delays due to sudden labor strike.', 'category' => 'Logistics', 'time' => '8 hours ago']
        ];

        return view('dashboard', compact(
            'totalShipments',
            'activeTransit',
            'redirected',
            'totalProfit',
            'chartMonths',
            'chartProfits',
            'chartRisks',
            'riskData',
            'commodities',
            'country',
            'aiRecommendations',
            'currencies',
            'news'
        ));
    }
}
