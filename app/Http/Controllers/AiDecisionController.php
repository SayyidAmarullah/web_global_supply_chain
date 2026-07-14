<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiRecommendation;
use App\Models\ProfitSimulation;
use App\Services\IntelligenceService;
use Illuminate\Support\Facades\Auth;

class AiDecisionController extends Controller
{
    protected $intelligenceService;

    public function __construct(IntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    public function index()
    {
        $user = Auth::user();

        // 1. Dashboard Metrics
        $dashboard = [
            'overall_score' => 87,
            'highest_profit_opp' => 'Export Wheat to Japan',
            'highest_risk' => 'Red Sea Transit (Geopolitical)',
            'best_export_country' => 'Japan',
            'best_import_country' => 'Brazil',
            'best_commodity' => 'Crude Oil',
            'best_route' => 'Shanghai - Yokohama',
            'confidence' => 92
        ];

        // 2. Risk & Opportunity Scores
        $scores = [
            'risk' => 35,
            'opportunity' => 88,
            'profit' => 94,
            'demand' => 80,
            'supply' => 60,
            'currency' => 75,
            'weather' => 20
        ];

        // 3. Smart Export Recommendations
        $exportRecs = [
            [
                'commodity' => 'Wheat',
                'target_country' => 'Japan',
                'selling_price' => '$320/Ton',
                'revenue' => '$3,200,000',
                'profit' => '$850,000',
                'shipping_cost' => '$120,000',
                'risk' => 'Low',
                'reason' => 'High demand due to poor domestic harvest in Japan. JPY is stable.'
            ],
            [
                'commodity' => 'Electronics',
                'target_country' => 'Germany',
                'selling_price' => '$1,200/Unit',
                'revenue' => '$12,000,000',
                'profit' => '$3,500,000',
                'shipping_cost' => '$450,000',
                'risk' => 'Medium',
                'reason' => 'Strong Euro currency valuation vs USD. Tech sector booming.'
            ]
        ];

        // 4. Smart Import Recommendations
        $importRecs = [
            [
                'commodity' => 'Coffee Beans',
                'supplier_country' => 'Brazil',
                'purchase_cost' => '$2,500/Ton',
                'shipping_cost' => '$200,000',
                'savings' => '$350,000',
                'risk' => 'Low',
                'reason' => 'BRL depreciation makes Brazilian exports 15% cheaper this month.'
            ]
        ];

        // 5. History
        $history = AiRecommendation::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('ai_decision.index', compact('dashboard', 'scores', 'exportRecs', 'importRecs', 'history'));
    }

    public function simulate(Request $request)
    {
        $user = Auth::user();
        $simulations = ProfitSimulation::where('user_id', $user->id)->latest()->get();

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'name' => 'required|string',
                'selling_price' => 'required|numeric',
                'purchase_cost' => 'required|numeric',
                'shipping_cost' => 'required|numeric',
                'insurance_cost' => 'required|numeric',
                'import_tax' => 'required|numeric',
                'export_tax' => 'required|numeric',
                'exchange_rate' => 'required|numeric'
            ]);

            $totalCost = $data['purchase_cost'] + $data['shipping_cost'] + $data['insurance_cost'] + $data['import_tax'] + $data['export_tax'];
            $revenue = $data['selling_price'] * $data['exchange_rate'];
            $profit = $revenue - $totalCost;
            $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

            $data['user_id'] = $user->id;
            $data['expected_revenue'] = $revenue;
            $data['expected_profit'] = $profit;
            $data['margin_percentage'] = $margin;

            ProfitSimulation::create($data);

            return redirect()->route('ai.simulate')->with('success', 'Simulation saved successfully.');
        }

        return view('ai_decision.simulate', compact('simulations'));
    }

    public function history()
    {
        $user = Auth::user();
        $recommendations = AiRecommendation::where('user_id', $user->id)->latest()->paginate(10);
        $simulations = ProfitSimulation::where('user_id', $user->id)->latest()->paginate(10);

        return view('ai_decision.history', compact('recommendations', 'simulations'));
    }
}
