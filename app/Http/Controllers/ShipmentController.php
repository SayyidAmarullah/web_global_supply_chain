<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\RedirectShipmentRequest;
use App\Models\Shipment;
use App\Repositories\Contracts\ShipmentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    protected $repository;

    public function __construct(ShipmentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $user = Auth::user();
        $shipments = $user->role === 'admin' ? $this->repository->all() : $user->shipments()->latest()->get();
        
        $stats = [
            'total' => $shipments->count(),
            'active' => $shipments->whereIn('status', ['Pending', 'Preparing', 'Loading', 'Departed', 'In Transit', 'Delayed', 'Redirected'])->count(),
            'import' => $shipments->where('type', 'import')->count(),
            'export' => $shipments->where('type', 'export')->count(),
            'revenue' => $shipments->sum('estimated_revenue'),
        ];

        return view('shipments.index', compact('shipments', 'stats'));
    }

    public function create()
    {
        return view('shipments.create');
    }

    public function store(StoreShipmentRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['shipment_number'] = 'SHP-' . strtoupper(uniqid());
        $data['status'] = 'Pending';
        
        $shipment = $this->repository->create($data);
        
        $shipment->activities()->create([
            'status' => 'Pending',
            'description' => 'Shipment created and pending processing.',
        ]);

        return redirect()->route('shipments.index')->with('success', 'Shipment created successfully.');
    }

    public function show(Shipment $shipment)
    {
        if (Auth::user()->role !== 'admin' && $shipment->user_id !== Auth::id()) {
            abort(403);
        }
        
        $shipment->load(['redirects', 'activities']);
        return view('shipments.show', compact('shipment'));
    }

    public function edit(Shipment $shipment)
    {
        if (Auth::user()->role !== 'admin' && $shipment->user_id !== Auth::id()) {
            abort(403);
        }
        return view('shipments.edit', compact('shipment'));
    }

    public function update(StoreShipmentRequest $request, Shipment $shipment)
    {
        if (Auth::user()->role !== 'admin' && $shipment->user_id !== Auth::id()) {
            abort(403);
        }
        
        $this->repository->update($shipment->id, $request->validated());
        
        return redirect()->route('shipments.show', $shipment)->with('success', 'Shipment updated successfully.');
    }

    public function destroy(Shipment $shipment)
    {
        if (Auth::user()->role !== 'admin' && $shipment->user_id !== Auth::id()) {
            abort(403);
        }
        
        $shipment->delete(); // Soft delete
        return redirect()->route('shipments.index')->with('success', 'Shipment archived successfully.');
    }

    public function redirect(Shipment $shipment)
    {
        if (Auth::user()->role !== 'admin' && $shipment->user_id !== Auth::id()) {
            abort(403);
        }

        $scenarios = [
            [
                'country' => 'Germany',
                'port' => 'Port of Hamburg',
                'reason' => 'AI detected high port congestion at ' . $shipment->destination_port . '. Rerouting to Hamburg provides a 12% profit increase due to local commodity shortages.',
                'profit_multiplier' => 1.12
            ],
            [
                'country' => 'United States',
                'port' => 'Port of Savannah',
                'reason' => 'Weather anomaly (category 4 storm) detected on the current route. Rerouting to Savannah avoids the storm while fulfilling secondary demand for ' . $shipment->commodity . '.',
                'profit_multiplier' => 1.05
            ],
            [
                'country' => 'Japan',
                'port' => 'Port of Yokohama',
                'reason' => 'Sudden inflation spike in ' . $shipment->destination_country . ' reduces purchasing power. Japanese markets offer a 20% premium for ' . $shipment->commodity . ' this week.',
                'profit_multiplier' => 1.20
            ],
            [
                'country' => 'South Africa',
                'port' => 'Port of Cape Town',
                'reason' => 'Geopolitical tensions in the Red Sea detected. Diverting via Cape of Good Hope increases transit time but secures cargo and capitalizes on regional shortages.',
                'profit_multiplier' => 1.08
            ]
        ];
        
        $scenario = $scenarios[array_rand($scenarios)];

        // Generate dynamic mock AI suggestion based on shipment
        $aiSuggestion = [
            'country' => $scenario['country'],
            'port' => $scenario['port'],
            'reason' => $scenario['reason'],
            'estimated_arrival' => now()->addDays(rand(10, 25))->format('Y-m-d'),
            'shipping_cost' => ($shipment->shipping_cost ?? rand(1000, 5000)) + rand(500, 2000),
            'estimated_profit' => ($shipment->estimated_profit ?? rand(10000, 50000)) * $scenario['profit_multiplier'],
        ];

        // Store into AiRecommendation for history tracking
        \App\Models\AiRecommendation::create([
            'user_id' => Auth::id(),
            'shipment_id' => $shipment->id,
            'type' => 'redirect',
            'recommended_country' => $aiSuggestion['country'],
            'recommended_port' => $aiSuggestion['port'],
            'recommended_commodity' => $shipment->commodity,
            'estimated_revenue' => $shipment->estimated_revenue ?? 0,
            'estimated_profit' => $aiSuggestion['estimated_profit'],
            'shipping_cost' => $aiSuggestion['shipping_cost'],
            'risk_score' => rand(10, 40),
            'opportunity_score' => rand(70, 99),
            'confidence_score' => rand(85, 98),
            'reason' => $aiSuggestion['reason'],
            'advantages' => 'Higher profit, avoided risk.',
            'status' => 'Pending'
        ]);

        return view('shipments.redirect', compact('shipment', 'aiSuggestion'));
    }

    public function storeRedirect(RedirectShipmentRequest $request, Shipment $shipment)
    {
        if (Auth::user()->role !== 'admin' && $shipment->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validated();
        
        // Save Redirect History
        $shipment->redirects()->create([
            'user_id' => Auth::id(),
            'old_destination_country' => $shipment->destination_country,
            'old_destination_port' => $shipment->destination_port,
            'new_destination_country' => $data['destination_country'],
            'new_destination_port' => $data['destination_port'],
            'reason' => $data['reason'],
            'old_estimated_arrival' => $shipment->estimated_arrival,
            'new_estimated_arrival' => $data['estimated_arrival'],
            'old_shipping_cost' => $shipment->shipping_cost,
            'new_shipping_cost' => $data['shipping_cost'],
            'old_estimated_profit' => $shipment->estimated_profit,
            'new_estimated_profit' => $data['estimated_profit'],
        ]);

        // Update Shipment Destination & Status
        $this->repository->update($shipment->id, [
            'destination_country' => $data['destination_country'],
            'destination_port' => $data['destination_port'],
            'estimated_arrival' => $data['estimated_arrival'],
            'shipping_cost' => $data['shipping_cost'],
            'estimated_profit' => $data['estimated_profit'],
            'status' => 'Redirected'
        ]);

        // Mark recommendation as accepted
        \App\Models\AiRecommendation::where('shipment_id', $shipment->id)
            ->where('status', 'Pending')
            ->update(['status' => 'Accepted']);

        $shipment->activities()->create([
            'status' => 'Redirected',
            'description' => 'Shipment redirected to ' . $data['destination_port'] . ', ' . $data['destination_country'] . ' due to: ' . $data['reason'],
        ]);

        return redirect()->route('shipments.show', $shipment)->with('success', 'Shipment redirected successfully.');
    }
}
