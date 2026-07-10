<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    public function index()
    {
        return view('map.index');
    }

    public function getMapData()
    {
        // Get user's visible shipments (or all for admin)
        $user = Auth::user();
        if ($user->role === 'admin') {
            $shipments = Shipment::whereIn('status', ['Pending', 'Departed', 'In Transit', 'Delayed', 'Redirected'])->get();
        } else {
            $shipments = $user->shipments()->whereIn('status', ['Pending', 'Departed', 'In Transit', 'Delayed', 'Redirected'])->get();
        }

        // Generate synthetic live coordinates and data for presentation
        // In a real application, these would come from AIS/GPS tracking APIs
        $liveShipments = $shipments->map(function ($ship) {
            return [
                'id' => $ship->id,
                'shipment_number' => $ship->shipment_number,
                'commodity' => $ship->commodity,
                'origin' => $ship->origin_country . ' (' . $ship->origin_port . ')',
                'destination' => $ship->destination_country . ' (' . $ship->destination_port . ')',
                'status' => $ship->status,
                'lat' => $ship->current_latitude ?? (mt_rand(-6000, 6000) / 100),
                'lng' => $ship->current_longitude ?? (mt_rand(-18000, 18000) / 100),
                'speed' => $ship->current_speed ?? mt_rand(10, 25),
                'heading' => $ship->current_heading ?? mt_rand(0, 360),
                'profit' => $ship->estimated_profit,
                'weather' => ['Clear', 'Rain', 'Storm'][mt_rand(0, 2)],
                'eta' => $ship->estimated_arrival ? $ship->estimated_arrival->format('M d, Y') : 'Unknown',
                'redirect_url' => route('shipments.redirect', $ship->id)
            ];
        });

        // Synthetic Ports
        $ports = [
            ['name' => 'Port of Singapore', 'lat' => 1.264, 'lng' => 103.840, 'congestion' => 'Low', 'weather' => 'Clear'],
            ['name' => 'Port of Rotterdam', 'lat' => 51.949, 'lng' => 4.148, 'congestion' => 'High', 'weather' => 'Rain'],
            ['name' => 'Port of Shanghai', 'lat' => 31.228, 'lng' => 121.475, 'congestion' => 'Medium', 'weather' => 'Clear'],
            ['name' => 'Port of Los Angeles', 'lat' => 33.728, 'lng' => -118.262, 'congestion' => 'High', 'weather' => 'Clear'],
            ['name' => 'Port of Hamburg', 'lat' => 53.548, 'lng' => 9.987, 'congestion' => 'Medium', 'weather' => 'Storm'],
        ];

        return response()->json([
            'shipments' => $liveShipments,
            'ports' => $ports
        ]);
    }
}
