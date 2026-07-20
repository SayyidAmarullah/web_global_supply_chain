@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <span class="material-symbols-outlined text-success fs-2">query_stats</span> 
                Data Visualization Dashboard
            </h1>
            <p class="text-muted fs-7 mt-1">Global macroeconomic trends and risk intelligence</p>
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
            <x-card title="Global GDP Growth" icon="public" glow="cyan">
                <div class="px-3 pb-3">
                    <h2 class="text-cyan-glow fw-bold mb-1">3.2%</h2>
                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25"><span class="material-symbols-outlined fs-8 align-middle">trending_up</span> +0.1% vs last year</span>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="Avg Global Inflation" icon="trending_up" glow="danger">
                <div class="px-3 pb-3">
                    <h2 class="text-danger fw-bold mb-1">5.8%</h2>
                    <span class="text-muted fs-8">Status: <strong class="text-danger">Elevated</strong></span>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="DXY Volatility Index" icon="currency_exchange" glow="purple">
                <div class="px-3 pb-3">
                    <h2 class="text-white fw-bold mb-1">105.5</h2>
                    <div class="d-flex gap-2">
                        <span class="text-muted fs-8"><strong class="text-purple-neon">High</strong> Market Variance</span>
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
        <div class="col-md-6">
            <x-card title="Global GDP Trend" icon="account_balance" glow="cyan">
                <div class="p-3" style="height: 300px;">
                    <canvas id="gdpTrendChart"></canvas>
                </div>
            </x-card>
        </div>
        <div class="col-md-6">
            <x-card title="Global Inflation Trend" icon="trending_up" glow="danger">
                <div class="p-3" style="height: 300px;">
                    <canvas id="inflationTrendChart"></canvas>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-4 mb-2">
        <div class="col-md-6">
            <x-card title="Currency Volatility Trend" icon="currency_exchange" glow="purple">
                <div class="p-3" style="height: 300px;">
                    <canvas id="currencyTrendChart"></canvas>
                </div>
            </x-card>
        </div>
        <div class="col-md-6">
            <x-card title="Global Risk Trend" icon="warning" glow="warning">
                <div class="p-3" style="height: 300px;">
                    <canvas id="riskTrendChart"></canvas>
                </div>
            </x-card>
        </div>
    </div>

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

    // 1. GDP Trend (Line Chart)
    const ctxGdp = document.getElementById('gdpTrendChart').getContext('2d');
    new Chart(ctxGdp, {
        type: 'line',
        data: {
            labels: ['2019', '2020', '2021', '2022', '2023', '2024'],
            datasets: [{
                label: 'Global GDP Growth (%)',
                data: [2.8, -3.1, 6.2, 3.4, 3.1, 3.2],
                borderColor: '#38BDF8',
                backgroundColor: 'rgba(56, 189, 248, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: chartOptions
    });

    // 2. Inflation Trend (Bar Chart)
    const ctxInflation = document.getElementById('inflationTrendChart').getContext('2d');
    new Chart(ctxInflation, {
        type: 'bar',
        data: {
            labels: ['2019', '2020', '2021', '2022', '2023', '2024'],
            datasets: [{
                label: 'Global Inflation Rate (%)',
                data: [3.5, 3.2, 4.7, 8.7, 6.8, 5.8],
                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                borderRadius: 4
            }]
        },
        options: chartOptions
    });

    // 3. Currency Volatility Trend (Line Chart)
    const ctxCurrency = document.getElementById('currencyTrendChart').getContext('2d');
    new Chart(ctxCurrency, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [
                {
                    label: 'USD Index (DXY)',
                    data: [103.5, 104.2, 103.8, 105.1, 104.7, 105.5],
                    borderColor: '#8B5CF6',
                    borderWidth: 2,
                    tension: 0.3
                },
                {
                    label: 'EUR/USD Volatility',
                    data: [1.08, 1.07, 1.09, 1.06, 1.08, 1.07],
                    borderColor: '#A0ABC0',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.3
                }
            ]
        },
        options: chartOptions
    });

    // 4. Global Risk Trend (Area Chart)
    const ctxRisk = document.getElementById('riskTrendChart').getContext('2d');
    new Chart(ctxRisk, {
        type: 'line',
        data: {
            labels: ['Q1', 'Q2', 'Q3', 'Q4', 'Q1 (Current)'],
            datasets: [{
                label: 'Global Risk Index',
                data: [35, 42, 58, 48, 55],
                borderColor: '#F59E0B',
                backgroundColor: 'rgba(245, 158, 11, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
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
