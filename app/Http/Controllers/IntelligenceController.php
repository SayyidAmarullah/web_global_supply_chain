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

    public function commodities()
    {
        // Placeholder for dedicated commodities view
        return redirect()->route('intelligence.index');
    }

    public function ports()
    {
        // Placeholder for dedicated ports view
        return redirect()->route('intelligence.index');
    }
}
