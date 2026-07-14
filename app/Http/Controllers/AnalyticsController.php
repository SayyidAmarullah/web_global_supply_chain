<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\AiRecommendation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $shipmentsQuery = $user->role === 'admin' ? Shipment::query() : $user->shipments();
        
        // Filter support
        if ($request->has('timeframe') && $request->timeframe != 'all') {
            if ($request->timeframe == 'month') {
                $shipmentsQuery->whereMonth('created_at', Carbon::now()->month);
            } elseif ($request->timeframe == 'year') {
                $shipmentsQuery->whereYear('created_at', Carbon::now()->year);
            }
        }
        
        $shipments = $shipmentsQuery->get();
        
        // Business KPIs
        $totalShipments = $shipments->count();
        $importShipments = $shipments->where('type', 'import')->count();
        $exportShipments = $shipments->where('type', 'export')->count();
        $completedShipments = $shipments->where('status', 'Completed')->count();
        
        $totalRevenue = $shipments->sum('estimated_revenue');
        $totalExpenses = $shipments->sum('shipping_cost') + $shipments->sum('import_tax') + $shipments->sum('export_tax'); // rough sum based on what's available
        $totalProfit = $shipments->sum('estimated_profit');
        
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
        
        // Advanced Analytics Data for Charts
        $monthlyRevenue = [];
        $monthlyProfit = [];
        for($i=1; $i<=12; $i++) {
            $monthlyShipments = $shipments->filter(function($s) use ($i) {
                return Carbon::parse($s->created_at)->month == $i;
            });
            $monthlyRevenue[] = $monthlyShipments->sum('estimated_revenue');
            $monthlyProfit[] = $monthlyShipments->sum('estimated_profit');
        }

        // Compare Import vs Export
        $importVsExport = [
            'import' => $importShipments,
            'export' => $exportShipments
        ];

        // Global Scores
        $globalRiskScore = rand(20, 60);
        $globalOppScore = rand(60, 95);

        // AI Rec Summary
        $aiRecs = AiRecommendation::where('user_id', $user->id)->count();

        // Forecast Data (Dummy predictive data based on trend)
        $revenueForecast = [];
        for($i=1; $i<=6; $i++) {
            $revenueForecast[] = rand(50000, 200000);
        }

        return view('analytics.index', compact(
            'totalShipments', 'importShipments', 'exportShipments', 'completedShipments',
            'totalRevenue', 'totalExpenses', 'totalProfit', 'profitMargin',
            'monthlyRevenue', 'monthlyProfit', 'importVsExport',
            'globalRiskScore', 'globalOppScore', 'aiRecs', 'revenueForecast', 'shipments'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'csv');
        $user = Auth::user();
        $shipments = $user->role === 'admin' ? Shipment::all() : $user->shipments;

        if ($type === 'csv') {
            $filename = "analytics_report_" . date('Y-m-d') . ".csv";
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            $columns = array('Shipment ID', 'Type', 'Commodity', 'Origin', 'Destination', 'Status', 'Revenue', 'Profit', 'Cost');

            $callback = function() use($shipments, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                foreach ($shipments as $shipment) {
                    $row['Shipment ID']  = $shipment->shipment_number;
                    $row['Type']         = $shipment->type;
                    $row['Commodity']    = $shipment->commodity;
                    $row['Origin']       = $shipment->origin_country;
                    $row['Destination']  = $shipment->destination_country;
                    $row['Status']       = $shipment->status;
                    $row['Revenue']      = $shipment->estimated_revenue;
                    $row['Profit']       = $shipment->estimated_profit;
                    $row['Cost']         = $shipment->shipping_cost;
                    fputcsv($file, array($row['Shipment ID'], $row['Type'], $row['Commodity'], $row['Origin'], $row['Destination'], $row['Status'], $row['Revenue'], $row['Profit'], $row['Cost']));
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // For PDF/Excel, we redirect to print view in this implementation
        return view('analytics.report_print', compact('shipments'));
    }
}
