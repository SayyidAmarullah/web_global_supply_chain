<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics Report</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { color: #333; }
        .text-right { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <h1>Global Trade Analytics Report</h1>
    <p>Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Shipment #</th>
                <th>Type</th>
                <th>Commodity</th>
                <th>Route</th>
                <th>Status</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipments as $shipment)
            <tr>
                <td>{{ $shipment->shipment_number }}</td>
                <td>{{ ucfirst($shipment->type) }}</td>
                <td>{{ $shipment->commodity }}</td>
                <td>{{ $shipment->origin_country }} -> {{ $shipment->destination_country }}</td>
                <td>{{ $shipment->status }}</td>
                <td class="text-right">${{ number_format($shipment->estimated_revenue, 2) }}</td>
                <td class="text-right">${{ number_format($shipment->estimated_profit, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
