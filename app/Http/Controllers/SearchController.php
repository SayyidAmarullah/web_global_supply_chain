<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->input('q'));

        if (empty($query)) {
            return redirect()->back()->with('error', 'Please enter a search term.');
        }

        // 1. Try to find an exact or partial match for a Shipment tracking number
        $shipment = Shipment::where('shipment_number', 'like', "%{$query}%")->first();
        if ($shipment) {
            return redirect()->route('shipments.show', $shipment);
        }

        // 2. Otherwise, assume it's a port, country, or commodity and redirect to Intelligence Hub
        // The ports page is the best hub for this, as it handles searching.
        return redirect()->route('intelligence.ports', ['search' => $query]);
    }
}
