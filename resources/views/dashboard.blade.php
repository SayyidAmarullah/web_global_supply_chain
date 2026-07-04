@extends('layouts.app')

@section('title', 'Overview')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bolder mb-1 text-secondary tracking-tight">Global Overview</h2>
        <p class="text-muted mb-0 fs-6">Real-time intelligence on your global supply chain network.</p>
    </div>
    
    <div class="d-flex gap-2">
        <button class="btn btn-light bg-white border shadow-sm rounded-pill px-3 py-2 d-flex align-items-center">
            <span class="material-symbols-outlined fs-5 me-2 text-muted">calendar_today</span>
            <span class="fs-7 fw-medium">Last 30 Days</span>
            <span class="material-symbols-outlined fs-5 ms-2 text-muted">expand_more</span>
        </button>
        <button class="btn btn-dark bg-secondary border-0 shadow-sm rounded-pill px-3 py-2 d-flex align-items-center">
            <span class="material-symbols-outlined fs-5 me-2">download</span>
            <span class="fs-7 fw-medium">Export Report</span>
        </button>
    </div>
</div>

<!-- Top Section: 2x2 Metrics + Risk Radar -->
<div class="row g-4 mb-4">
    <!-- Left Column: 2x2 Metrics -->
    <div class="col-xl-8">
        <div class="row g-4 h-100">
            <div class="col-md-6">
                <div class="glass-widget p-4 widget-gradient-blue map-bg h-100">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="metric-icon-box bg-white bg-opacity-25">
                            <span class="material-symbols-outlined fs-3">public</span>
                        </div>
                        <span class="badge bg-white text-primary rounded-pill px-2 py-1 fs-7 fw-bold d-flex align-items-center">
                            <span class="material-symbols-outlined fs-6 me-1">trending_up</span> +14%
                        </span>
                    </div>
                    <p class="text-white-50 fw-semibold mb-1 fs-7 text-uppercase tracking-wider">Active Shipments</p>
                    <h2 class="fw-bolder mb-0 display-6">12,458</h2>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="glass-widget p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="metric-icon-box bg-success bg-opacity-10 text-success">
                            <span class="material-symbols-outlined fs-3">local_shipping</span>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fs-7 fw-bold d-flex align-items-center">
                            <span class="material-symbols-outlined fs-6 me-1">check_circle</span> 94%
                        </span>
                    </div>
                    <p class="text-muted fw-semibold mb-1 fs-7 text-uppercase tracking-wider">On-Time Delivery</p>
                    <h2 class="fw-bolder mb-0 text-secondary display-6">3,240</h2>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="glass-widget p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="metric-icon-box bg-primary bg-opacity-10 text-primary">
                            <span class="material-symbols-outlined fs-3">account_balance</span>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fs-7 fw-bold d-flex align-items-center">
                            <span class="material-symbols-outlined fs-6 me-1">trending_up</span> 8.2%
                        </span>
                    </div>
                    <p class="text-muted fw-semibold mb-1 fs-7 text-uppercase tracking-wider">Cargo Value (USD)</p>
                    <h2 class="fw-bolder mb-0 text-secondary display-6">$8.4M</h2>
                </div>
            </div>

            <div class="col-md-6">
                <div class="glass-widget p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="metric-icon-box bg-warning bg-opacity-10 text-warning">
                            <span class="material-symbols-outlined fs-3">warning</span>
                        </div>
                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 fs-7 fw-bold d-flex align-items-center">
                            <span class="material-symbols-outlined fs-6 me-1">arrow_upward</span> +2
                        </span>
                    </div>
                    <p class="text-muted fw-semibold mb-1 fs-7 text-uppercase tracking-wider">Critical Alerts</p>
                    <h2 class="fw-bolder mb-0 text-secondary display-6">14</h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Risk Radar -->
    <div class="col-xl-4">
        <div class="glass-widget p-4 d-flex flex-column h-100">
            <h5 class="fw-bold mb-4 text-secondary">Risk Intelligence Radar</h5>
            <div class="flex-grow-1 d-flex justify-content-center align-items-center" style="min-height: 250px;">
                <canvas id="riskRadarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Trend Chart + Table Side by Side -->
<div class="row g-4">
    <!-- Logistics Volume -->
    <div class="col-xl-5">
        <div class="glass-widget p-4 d-flex flex-column h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-secondary">Global Volume</h5>
                <button class="btn btn-sm btn-light bg-white border rounded-pill px-3 fw-medium d-flex align-items-center">
                    2026 <span class="material-symbols-outlined fs-6 ms-1">expand_more</span>
                </button>
            </div>
            <div class="flex-grow-1" style="min-height: 300px;">
                <canvas id="shipmentTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Live Table -->
    <div class="col-xl-7">
        <div class="glass-widget overflow-hidden h-100 d-flex flex-column">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white bg-opacity-50">
                <h5 class="fw-bold mb-0 text-secondary d-flex align-items-center">
                    <span class="material-symbols-outlined text-primary me-2">satellite_alt</span>
                    Live Monitored Routes
                </h5>
                <a href="#" class="text-decoration-none fw-medium fs-7 text-primary d-flex align-items-center">
                    View Full Map <span class="material-symbols-outlined fs-6 ms-1">arrow_forward</span>
                </a>
            </div>
            <div class="table-responsive flex-grow-1">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Vessel / Shipment</th>
                            <th>Route</th>
                            <th>Status & ETA</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                        <span class="material-symbols-outlined fs-5">directions_boat</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-secondary">MSC Isabella</div>
                                        <div class="fs-7 text-muted">SHP-98302</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium text-secondary"><span class="me-1">🇨🇳</span> Shanghai</div>
                                <div class="fs-7 text-muted"><span class="material-symbols-outlined text-muted" style="font-size: 14px; vertical-align: middle;">arrow_downward</span> to Los Angeles <span class="me-1">🇺🇸</span></div>
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 mb-1">On Schedule</span>
                                <div class="fs-7 text-secondary fw-medium">Jul 12, 2026</div>
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-light bg-white border rounded-circle p-1 shadow-sm"><span class="material-symbols-outlined fs-5 text-secondary">visibility</span></button>
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 me-3">
                                        <span class="material-symbols-outlined fs-5">directions_boat</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-secondary">Ever Given</div>
                                        <div class="fs-7 text-muted">SHP-98284</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium text-secondary"><span class="me-1">🇩🇪</span> Hamburg</div>
                                <div class="fs-7 text-muted"><span class="material-symbols-outlined text-muted" style="font-size: 14px; vertical-align: middle;">arrow_downward</span> to Singapore <span class="me-1">🇸🇬</span></div>
                            </td>
                            <td>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1 mb-1">Storm Warning</span>
                                <div class="fs-7 text-danger fw-medium">Delayed (Jul 05)</div>
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-light bg-white border rounded-circle p-1 shadow-sm"><span class="material-symbols-outlined fs-5 text-secondary">visibility</span></button>
                            </td>
                        </tr>

                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3">
                                        <span class="material-symbols-outlined fs-5">directions_boat</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-secondary">CMA CGM Antoine</div>
                                        <div class="fs-7 text-muted">SHP-98251</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium text-secondary"><span class="me-1">🇦🇪</span> Jebel Ali</div>
                                <div class="fs-7 text-muted"><span class="material-symbols-outlined text-muted" style="font-size: 14px; vertical-align: middle;">arrow_downward</span> to Rotterdam <span class="me-1">🇳🇱</span></div>
                            </td>
                            <td>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2 py-1 mb-1">High Congestion</span>
                                <div class="fs-7 text-warning fw-medium">Deviation (Jul 18)</div>
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-light bg-white border rounded-circle p-1 shadow-sm"><span class="material-symbols-outlined fs-5 text-secondary">visibility</span></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if(typeof Chart !== 'undefined') {
        
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#94a3b8'; // text-muted

        // Custom Line Chart for Shipment Trend
        const ctxTrend = document.getElementById('shipmentTrendChart').getContext('2d');
        
        const gradientImport = ctxTrend.createLinearGradient(0, 0, 0, 400);
        gradientImport.addColorStop(0, 'rgba(11, 94, 215, 0.5)');
        gradientImport.addColorStop(1, 'rgba(11, 94, 215, 0.0)');
        
        const gradientExport = ctxTrend.createLinearGradient(0, 0, 0, 400);
        gradientExport.addColorStop(0, 'rgba(56, 189, 248, 0.5)');
        gradientExport.addColorStop(1, 'rgba(56, 189, 248, 0.0)');

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'Inbound',
                        data: [12, 19, 15, 22, 18, 25],
                        borderColor: '#0B5ED7',
                        backgroundColor: gradientImport,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0B5ED7',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Outbound',
                        data: [10, 15, 13, 18, 16, 21],
                        borderColor: '#38BDF8',
                        backgroundColor: gradientExport,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#38BDF8',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: { usePointStyle: true, boxWidth: 8, padding: 10 }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(10, 37, 64, 0.9)',
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 13 }
                    }
                },
                scales: {
                    x: { grid: { display: false, drawBorder: false } },
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false, borderDash: [5, 5] },
                        beginAtZero: true
                    }
                },
                interaction: { mode: 'index', intersect: false }
            }
        });

        // Unique Radar Chart for Risk Analysis
        const ctxRadar = document.getElementById('riskRadarChart').getContext('2d');
        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: ['Weather', 'Piracy', 'Congest.', 'Currency', 'Politic.', 'Customs'],
                datasets: [{
                    label: 'Global Risk Level',
                    data: [85, 20, 65, 45, 30, 50],
                    backgroundColor: 'rgba(14, 165, 233, 0.2)',
                    borderColor: '#0ea5e9',
                    pointBackgroundColor: '#0ea5e9',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#0ea5e9',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: 'rgba(10, 37, 64, 0.9)', padding: 12, cornerRadius: 12 }
                },
                scales: {
                    r: {
                        angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' },
                        pointLabels: { font: { size: 12, family: "'Plus Jakarta Sans', sans-serif", weight: '600' }, color: '#94a3b8' },
                        ticks: { display: false, max: 100 }
                    }
                }
            }
        });
    }
});
</script>
@endpush
