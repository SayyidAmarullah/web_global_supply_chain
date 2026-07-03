<?php

namespace App\Repositories\Eloquent;

use App\Models\Shipment;
use App\Repositories\Contracts\ShipmentRepositoryInterface;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    protected $model;

    public function __construct(Shipment $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $shipment = $this->find($id);
        $shipment->update($data);
        return $shipment;
    }

    public function delete($id)
    {
        $shipment = $this->find($id);
        return $shipment->delete();
    }

    public function getActiveRoutes()
    {
        // Example implementation for dashboard
        return $this->model->whereIn('status', ['in_transit', 'delayed', 'at_risk'])
                           ->orderBy('risk_score', 'desc')
                           ->take(5)
                           ->get();
    }

    public function calculateGlobalRiskScore()
    {
        return $this->model->where('status', 'in_transit')->avg('risk_score') ?? 0;
    }
}
