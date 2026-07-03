@extends('layouts.app')

@section('title', 'Global Overview')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Global Command Center</h2>
        <p class="text-muted mb-0">Real-time overview of international logistics and supply chain risks.</p>
    </div>
    <div>
        <button class="btn btn-primary d-flex align-items-center px-4 py-2 rounded-pill shadow-sm">
            <span class="material-symbols-outlined me-2 fs-5">add</span>
            New Shipment
        </button>
    </div>
</div>

<!-- Executive Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <p class="text-muted fw-semibold mb-1 fs-7 text-uppercase">Total Shipments</p>
                    <h3 class="fw-bold text-secondary mb-0">12,458</h3>
                </div>
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                    <span class="material-symbols-outlined">directions_boat</span>
                </div>
            </div>
            <div class="d-flex align-items-center text-success fw-medium fs-7">
                <span class="material-symbols-outlined fs-6 me-1">trending_up</span>
                <span>+14.5% vs last month</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <p class="text-muted fw-semibold mb-1 fs-7 text-uppercase">Cargo In Transit</p>
                    <h3 class="fw-bold text-secondary mb-0">3,240</h3>
                </div>
                <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                    <span class="material-symbols-outlined">schedule</span>
                </div>
            </div>
            <div class="d-flex align-items-center text-success fw-medium fs-7">
                <span class="material-symbols-outlined fs-6 me-1">trending_up</span>
                <span>+2.1% vs last week</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="glass-card p-4 h-100 border-danger border-opacity-25">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <p class="text-danger fw-semibold mb-1 fs-7 text-uppercase">Risk Alerts</p>
                    <h3 class="fw-bold text-danger mb-0">14</h3>
                </div>
                <div class="bg-danger bg-opacity-10 p-2 rounded-3 text-danger">
                    <span class="material-symbols-outlined">warning</span>
                </div>
            </div>
            <div class="d-flex align-items-center text-danger fw-medium fs-7">
                <span class="material-symbols-outlined fs-6 me-1">trending_up</span>
                <span>+4 severe weather alerts</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <p class="text-muted fw-semibold mb-1 fs-7 text-uppercase">Global Revenue</p>
                    <h3 class="fw-bold text-secondary mb-0">$8.4M</h3>
                </div>
                <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                    <span class="material-symbols-outlined">payments</span>
                </div>
            </div>
            <div class="d-flex align-items-center text-success fw-medium fs-7">
                <span class="material-symbols-outlined fs-6 me-1">trending_up</span>
                <span>+8.2% vs last quarter</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4">Shipment Trend (Import vs Export)</h5>
            <canvas id="shipmentTrendChart" height="100"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-4">Geopolitical & Weather Risks</h5>
            <canvas id="riskDistributionChart" height="200"></canvas>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <div class="glass-card p-0 overflow-hidden">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Active Monitored Routes</h5>
                <button class="btn btn-sm btn-outline-secondary">View All</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4 fw-medium border-0">Shipment ID</th>
                            <th class="fw-medium border-0">Origin</th>
                            <th class="fw-medium border-0">Destination</th>
                            <th class="fw-medium border-0">Status</th>
                            <th class="fw-medium border-0">Risk Score</th>
                            <th class="pe-4 text-end fw-medium border-0">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 fw-medium text-primary">#SHP-98302</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fs-4 me-2">🇨🇳</span> Shanghai Port
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fs-4 me-2">🇺🇸</span> Los Angeles Port
                                </div>
                            </td>
                            <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">In Transit</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress w-100 me-2" style="height: 6px;">
                                        <div class="progress-bar bg-success progress-animated" style="width: 15%"></div>
                                    </div>
                                    <span class="fs-7 fw-medium text-muted">15/100</span>
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-light btn-sm rounded-circle"><span class="material-symbols-outlined fs-6">more_vert</span></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-medium text-primary">#SHP-98284</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fs-4 me-2">🇩🇪</span> Hamburg Port
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fs-4 me-2">🇸🇬</span> Singapore Port
                                </div>
                            </td>
                            <td><span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">Delayed</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress w-100 me-2" style="height: 6px;">
                                        <div class="progress-bar bg-danger progress-animated" style="width: 85%"></div>
                                    </div>
                                    <span class="fs-7 fw-medium text-danger">85/100</span>
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-light btn-sm rounded-circle"><span class="material-symbols-outlined fs-6">more_vert</span></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-medium text-primary">#SHP-98251</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fs-4 me-2">🇦🇪</span> Jebel Ali Port
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fs-4 me-2">🇳🇱</span> Rotterdam Port
                                </div>
                            </td>
                            <td><span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">At Risk</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress w-100 me-2" style="height: 6px;">
                                        <div class="progress-bar bg-warning progress-animated" style="width: 60%"></div>
                                    </div>
                                    <span class="fs-7 fw-medium text-warning">60/100</span>
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-light btn-sm rounded-circle"><span class="material-symbols-outlined fs-6">more_vert</span></button>
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
    // Check if Chart is available
    if(typeof Chart !== 'undefined') {
        
        // Shipment Trend Chart
        const ctxTrend = document.getElementById('shipmentTrendChart').getContext('2d');
        
        // Gradient for Import
        const gradientImport = ctxTrend.createLinearGradient(0, 0, 0, 400);
        gradientImport.addColorStop(0, 'rgba(11, 94, 215, 0.4)');
        gradientImport.addColorStop(1, 'rgba(11, 94, 215, 0.05)');
        
        // Gradient for Export
        const gradientExport = ctxTrend.createLinearGradient(0, 0, 0, 400);
        gradientExport.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        gradientExport.addColorStop(1, 'rgba(16, 185, 129, 0.05)');

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Imports',
                        data: [1200, 1900, 1500, 2200, 1800, 2500, 2100, 2800, 2400, 3100, 2900, 3500],
                        borderColor: '#0B5ED7',
                        backgroundColor: gradientImport,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0B5ED7',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Exports',
                        data: [1000, 1500, 1300, 1800, 1600, 2100, 1900, 2400, 2200, 2700, 2500, 3100],
                        borderColor: '#10B981',
                        backgroundColor: gradientExport,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#10B981',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
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
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: "'Poppins', sans-serif",
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0A2540',
                        titleFont: { family: "'Poppins', sans-serif", size: 13 },
                        bodyFont: { family: "'Poppins', sans-serif", size: 13 },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        intersect: false,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { family: "'Poppins', sans-serif" } }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false,
                            borderDash: [5, 5]
                        },
                        ticks: { font: { family: "'Poppins', sans-serif" } }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
            }
        });

        // Risk Distribution Chart (Doughnut)
        const ctxRisk = document.getElementById('riskDistributionChart').getContext('2d');
        new Chart(ctxRisk, {
            type: 'doughnut',
            data: {
                labels: ['Weather Risk', 'Port Congestion', 'Geopolitical', 'Currency Fluctuations', 'Low Risk'],
                datasets: [{
                    data: [15, 25, 10, 5, 45],
                    backgroundColor: [
                        '#38BDF8', // Weather
                        '#F59E0B', // Congestion
                        '#EF4444', // Geopolitical
                        '#8B5CF6', // Currency
                        '#10B981'  // Low risk
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: { family: "'Poppins', sans-serif", size: 11 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0A2540',
                        bodyFont: { family: "'Poppins', sans-serif", size: 13 },
                        padding: 10,
                        cornerRadius: 8,
                    }
                }
            }
        });
    }
});
</script>
@endpush
