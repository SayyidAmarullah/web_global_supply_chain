<?php

namespace App\Services;

use App\Repositories\Contracts\ShipmentRepositoryInterface;
use Illuminate\Support\Facades\Log;

class ShipmentService
{
    protected $shipmentRepository;

    public function __construct(ShipmentRepositoryInterface $shipmentRepository)
    {
        $this->shipmentRepository = $shipmentRepository;
    }

    public function createShipment(array $data)
    {
        try {
            // Business logic for risk calculation before saving
            $data['risk_score'] = $this->calculateInitialRisk($data['origin'], $data['destination']);
            $data['status'] = 'pending';
            
            return $this->shipmentRepository->create($data);
        } catch (\Exception $e) {
            Log::error('Error creating shipment: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getDashboardMetrics()
    {
        return [
            'active_routes' => $this->shipmentRepository->getActiveRoutes(),
            'global_risk_average' => $this->shipmentRepository->calculateGlobalRiskScore(),
            // Other metrics...
        ];
    }

    private function calculateInitialRisk($origin, $destination)
    {
        // Mock risk calculation based on origin/destination factors, weather API, etc.
        return rand(10, 40); // Base risk
    }
}
