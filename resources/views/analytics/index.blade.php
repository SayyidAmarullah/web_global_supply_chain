@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <span class="material-symbols-outlined text-success fs-2">query_stats</span> 
                Executive Analytics & Reporting
            </h1>
            <p class="text-muted fs-7 mt-1">Business intelligence, KPI monitoring, and forecasting</p>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2 px-3 py-2" type="button" data-bs-toggle="dropdown">
                    <span class="material-symbols-outlined fs-5">filter_list</span> 
                    {{ request('timeframe', 'All Time') }}
                </button>
                <ul class="dropdown-menu dropdown-menu-dark glass-panel">
                    <li><a class="dropdown-item" href="{{ route('analytics.index', ['timeframe' => 'all']) }}">All Time</a></li>
                    <li><a class="dropdown-item" href="{{ route('analytics.index', ['timeframe' => 'year']) }}">This Year</a></li>
                    <li><a class="dropdown-item" href="{{ route('analytics.index', ['timeframe' => 'month']) }}">This Month</a></li>
                </ul>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle d-flex align-items-center gap-2 px-3 py-2" type="button" data-bs-toggle="dropdown">
                    <span class="material-symbols-outlined fs-5">download</span> Export Report
                </button>
                <ul class="dropdown-menu dropdown-menu-dark glass-panel">
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" onclick="window.print()"><span class="material-symbols-outlined fs-6 text-danger">picture_as_pdf</span> Export as PDF</a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('analytics.export', ['type' => 'csv']) }}"><span class="material-symbols-outlined fs-6 text-success">grid_on</span> Export as CSV</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Top KPIs -->
    <div class="row g-4 mb-2">
        <div class="col-md-3">
            <x-card title="Total Revenue" icon="account_balance_wallet" glow="success">
                <div class="px-3 pb-3">
                    <h2 class="text-success fw-bold mb-1">${{ number_format($totalRevenue, 0) }}</h2>
                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25"><span class="material-symbols-outlined fs-8 align-middle">trending_up</span> +12.4% vs last period</span>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="Net Profit" icon="savings" glow="cyan">
                <div class="px-3 pb-3">
                    <h2 class="text-white fw-bold mb-1">${{ number_format($totalProfit, 0) }}</h2>
                    <span class="text-muted fs-8">Margin: <strong class="text-cyan-glow">{{ number_format($profitMargin, 1) }}%</strong></span>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="Total Shipments" icon="local_shipping" glow="purple">
                <div class="px-3 pb-3">
                    <h2 class="text-white fw-bold mb-1">{{ $totalShipments }}</h2>
                    <div class="d-flex gap-2">
                        <span class="text-muted fs-8"><strong class="text-white">{{ $completedShipments }}</strong> Completed</span>
                    </div>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="Global Risk Index" icon="warning" glow="warning">
                <div class="px-3 pb-3">
                    <h2 class="text-warning fw-bold mb-1">{{ $globalRiskScore }}/100</h2>
                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25">Moderate Risk</span>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row g-4 mb-2">
        <div class="col-md-8">
            <x-card title="Revenue & Profit Trend" icon="monitoring" glow="cyan">
                <div class="p-3" style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </x-card>
        </div>
        <div class="col-md-4">
            <x-card title="Import vs Export" icon="pie_chart" glow="purple">
                <div class="p-3" style="height: 300px;">
                    <canvas id="tradeTypeChart"></canvas>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-4 mb-2">
        <div class="col-md-4">
            <x-card title="AI Confidence & Opportunity" icon="radar" glow="success">
                <div class="p-3" style="height: 300px;">
                    <canvas id="radarChart"></canvas>
                </div>
            </x-card>
        </div>
        <div class="col-md-8">
            <x-card title="Revenue Forecast (Next 6 Months)" icon="online_prediction" glow="warning">
                <div class="p-3" style="height: 300px;">
                    <canvas id="forecastChart"></canvas>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Search & Data Table -->
    <x-card title="Shipment Analytical Data" icon="table_chart">
        <div class="p-3">
            <div class="d-flex mb-3 gap-2">
                <input type="text" class="form-control bg-dark border-secondary text-white w-25" placeholder="Search Shipment, Country, Port...">
                <button class="btn btn-outline-secondary">Search</button>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr class="border-bottom border-secondary border-opacity-25">
                            <th class="text-muted fs-8 text-uppercase pb-2">Shipment</th>
                            <th class="text-muted fs-8 text-uppercase pb-2">Type</th>
                            <th class="text-muted fs-8 text-uppercase pb-2">Commodity</th>
                            <th class="text-muted fs-8 text-uppercase pb-2">Revenue</th>
                            <th class="text-muted fs-8 text-uppercase pb-2">Profit</th>
                            <th class="text-muted fs-8 text-uppercase pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments->take(10) as $shipment)
                        <tr>
                            <td class="text-white fw-bold">{{ $shipment->shipment_number }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($shipment->type) }}</span></td>
                            <td class="text-muted">{{ $shipment->commodity }}</td>
                            <td class="text-success fw-bold">${{ number_format($shipment->estimated_revenue, 0) }}</td>
                            <td class="text-cyan-glow fw-bold">${{ number_format($shipment->estimated_profit, 0) }}</td>
                            <td>
                                @if($shipment->status === 'Redirected')
                                    <span class="badge bg-purple bg-opacity-25 text-purple-neon">{{ $shipment->status }}</span>
                                @elseif($shipment->status === 'Completed')
                                    <span class="badge bg-success bg-opacity-25 text-success">{{ $shipment->status }}</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-25 text-warning">{{ $shipment->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No data available for the selected timeframe.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-card>
</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shared Options
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: { color: '#A0ABC0' }
            }
        },
        scales: {
            x: {
                grid: { color: 'rgba(255,255,255,0.05)' },
                ticks: { color: '#A0ABC0' }
            },
            y: {
                grid: { color: 'rgba(255,255,255,0.05)' },
                ticks: { color: '#A0ABC0' }
            }
        }
    };

    // 1. Revenue & Profit Trend (Area/Line)
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [
                {
                    label: 'Revenue',
                    data: {!! json_encode($monthlyRevenue) !!},
                    borderColor: '#38BDF8',
                    backgroundColor: 'rgba(56, 189, 248, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Profit',
                    data: {!! json_encode($monthlyProfit) !!},
                    borderColor: '#22C55E',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: chartOptions
    });

    // 2. Import vs Export (Pie Chart)
    const ctxTradeType = document.getElementById('tradeTypeChart').getContext('2d');
    new Chart(ctxTradeType, {
        type: 'doughnut',
        data: {
            labels: ['Import', 'Export'],
            datasets: [{
                data: [{{ $importVsExport['import'] }}, {{ $importVsExport['export'] }}],
                backgroundColor: ['#8B5CF6', '#38BDF8'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#A0ABC0' } }
            },
            cutout: '70%'
        }
    });

    // 3. AI Radar Chart
    const ctxRadar = document.getElementById('radarChart').getContext('2d');
    new Chart(ctxRadar, {
        type: 'radar',
        data: {
            labels: ['Opportunity', 'Demand', 'Supply', 'Profitability', 'Stability'],
            datasets: [{
                label: 'Global Metrics',
                data: [{{ $globalOppScore }}, 85, 60, 92, 70],
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                borderColor: '#22C55E',
                pointBackgroundColor: '#22C55E'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: 'rgba(255,255,255,0.1)' },
                    grid: { color: 'rgba(255,255,255,0.1)' },
                    pointLabels: { color: '#A0ABC0' },
                    ticks: { display: false }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // 4. Forecast Chart (Bar Chart)
    const ctxForecast = document.getElementById('forecastChart').getContext('2d');
    new Chart(ctxForecast, {
        type: 'bar',
        data: {
            labels: ['Month +1', 'Month +2', 'Month +3', 'Month +4', 'Month +5', 'Month +6'],
            datasets: [{
                label: 'Projected Revenue',
                data: {!! json_encode($revenueForecast) !!},
                backgroundColor: 'rgba(245, 158, 11, 0.8)',
                borderRadius: 4
            }]
        },
        options: chartOptions
    });
});
</script>

<style>
@media print {
    body { background: white; color: black; }
    .top-nav, .left-sidebar, .bottom-ticker-container, .ai-assistant-orb, .btn { display: none !important; }
    .content-area { overflow: visible !important; height: auto !important; position: static !important; }
    .glass-panel { box-shadow: none !important; border: 1px solid #ccc !important; background: white !important; }
    * { color: black !important; text-shadow: none !important; }
    .badge { border: 1px solid #000 !important; color: #000 !important; }
}
</style>
@endsection
