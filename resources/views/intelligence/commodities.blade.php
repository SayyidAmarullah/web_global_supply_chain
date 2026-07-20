@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>
<style>
/* Custom Dark Glass Table Styles */
.glass-table {
    background: rgba(10, 17, 40, 0.4);
    backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.trend-up {
    color: #34d399; /* Emerald 400 */
    background: rgba(52, 211, 153, 0.1);
}
.trend-down {
    color: #f87171; /* Red 400 */
    background: rgba(248, 113, 113, 0.1);
}
.trend-flat {
    color: #9ca3af; /* Gray 400 */
    background: rgba(156, 163, 175, 0.1);
}

/* Light Mode Overrides */
:root[data-theme="light"] .glass-panel {
    border: 1px solid rgba(0,0,0,0.05) !important;
    background: rgba(255, 255, 255, 0.8) !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}

/* Pagination Overrides */
.custom-pagination .page-link {
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.1);
    color: #9ca3af;
}
.custom-pagination .page-link:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
}
.custom-pagination .page-item.active .page-link {
    background: #eab308; /* warning */
    border-color: #eab308;
    color: #000;
    font-weight: bold;
}
.custom-pagination .page-item.disabled .page-link {
    background: transparent;
    border-color: rgba(255,255,255,0.05);
    color: rgba(255,255,255,0.2);
}
:root[data-theme="light"] .custom-pagination .page-link {
    background: #fff;
    border-color: #e5e7eb;
    color: #374151;
}
:root[data-theme="light"] .custom-pagination .page-item.active .page-link {
    color: #000;
}
:root[data-theme="light"] .text-white {
    color: #111827 !important;
}
:root[data-theme="light"] .table-dark {
    --bs-table-bg: transparent;
    --bs-table-color: #111827;
}
:root[data-theme="light"] .table-dark tbody tr:hover td {
    background: rgba(0,0,0,0.03) !important;
}
:root[data-theme="light"] .table-dark td {
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
:root[data-theme="light"] thead {
    background: rgba(243, 244, 246, 0.8) !important;
    border-bottom: 1px solid rgba(0,0,0,0.1) !important;
}
:root[data-theme="light"] .text-muted {
    color: #6b7280 !important;
}
</style>

<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                Global Commodities Market
            </h2>
            <p class="text-muted mb-0 fs-7">Real-time pricing, volatility trends, and AI-driven market predictions</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn glass-pill text-white border-secondary">
                <span class="material-symbols-outlined fs-6 me-1">download</span> Export Report
            </button>
            <button class="btn glass-pill text-warning border-warning" onclick="window.location.reload()">
                <span class="material-symbols-outlined fs-6 me-1">refresh</span> Sync Market
            </button>
        </div>
    </header>

    <!-- KPI Summary -->
    <div class="row g-4 mb-2">
        <div class="col-md-3">
            <div class="glass-panel p-4 rounded-4 h-100 position-relative overflow-hidden d-flex flex-column justify-content-center">
                <div class="position-absolute top-50 translate-middle-y end-0 pe-2 opacity-25" style="transform: translateY(-50%) translateX(10%);">
                    <span class="material-symbols-outlined text-muted" style="font-size: 80px;">candlestick_chart</span>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-muted text-uppercase fw-bold fs-8 mb-1 tracking-wide">Tracked Assets</h6>
                    <h2 class="text-white fw-bold mb-0" style="font-size: 2.5rem;">{{ $stats['total'] }}</h2>
                    <div class="text-success fs-8 mt-2 d-flex align-items-center gap-1"><span class="material-symbols-outlined fs-8">sync</span> Live feeds active</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-panel p-4 rounded-4 h-100 position-relative overflow-hidden d-flex flex-column justify-content-center">
                <div class="position-absolute top-50 translate-middle-y end-0 pe-2 opacity-25" style="transform: translateY(-50%) translateX(10%);">
                    <span class="material-symbols-outlined text-success" style="font-size: 80px;">trending_up</span>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-muted text-uppercase fw-bold fs-8 mb-1 tracking-wide">Top Performer</h6>
                    <h2 class="text-success fw-bold mb-0 text-truncate" style="font-size: 1.8rem;" title="{{ $stats['top']['name'] }}">{{ $stats['top']['name'] }}</h2>
                    <div class="text-success fs-8 mt-2 d-flex align-items-center gap-1"><span class="material-symbols-outlined fs-8">arrow_upward</span> +{{ $stats['top']['trend'] }}% / 24h</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-panel p-4 rounded-4 h-100 position-relative overflow-hidden d-flex flex-column justify-content-center">
                <div class="position-absolute top-50 translate-middle-y end-0 pe-2 opacity-25" style="transform: translateY(-50%) translateX(10%);">
                    <span class="material-symbols-outlined text-danger" style="font-size: 80px;">trending_down</span>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-muted text-uppercase fw-bold fs-8 mb-1 tracking-wide">Market Pullback</h6>
                    <h2 class="text-danger fw-bold mb-0 text-truncate" style="font-size: 1.8rem;" title="{{ $stats['bottom']['name'] }}">{{ $stats['bottom']['name'] }}</h2>
                    <div class="text-danger fs-8 mt-2 d-flex align-items-center gap-1"><span class="material-symbols-outlined fs-8">arrow_downward</span> {{ $stats['bottom']['trend'] }}% / 24h</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-panel p-4 rounded-4 h-100 position-relative overflow-hidden d-flex flex-column justify-content-center">
                <div class="position-absolute top-50 translate-middle-y end-0 pe-2 opacity-25" style="transform: translateY(-50%) translateX(10%);">
                    <span class="material-symbols-outlined text-warning" style="font-size: 80px;">warning</span>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-muted text-uppercase fw-bold fs-8 mb-1 tracking-wide">High Risk Assets</h6>
                    <h2 class="text-warning fw-bold mb-0" style="font-size: 2.5rem;">{{ $stats['highRiskCount'] }}</h2>
                    <div class="text-warning fs-8 mt-2 d-flex align-items-center gap-1"><span class="material-symbols-outlined fs-8">gpp_maybe</span> Volatility Warning</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 flex-grow-1">
        <!-- Commodities List -->
        <div class="col-lg-8 d-flex flex-column gap-4">
            <div class="glass-panel rounded-4 p-4 flex-grow-1 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-white fw-bold mb-0">Live Market Pricing</h5>
                    <div class="input-group" style="width: 250px;">
                        <span class="input-group-text bg-transparent border-secondary text-muted border-end-0">
                            <span class="material-symbols-outlined fs-6">search</span>
                        </span>
                        <input type="text" class="form-control bg-transparent border-secondary border-start-0 text-white" placeholder="Search asset...">
                    </div>
                </div>

                <div class="table-responsive flex-grow-1" style="overflow-y: auto; min-height: 0;">
                    <table class="table table-dark table-hover align-middle mb-0" style="min-width: 800px;">
                        <thead class="text-muted fs-8 text-uppercase position-sticky top-0 z-1" style="background: var(--glass-bg); backdrop-filter: blur(24px); border-bottom: 1px solid var(--glass-border);">
                            <tr>
                                <th class="py-3 font-weight-normal border-0">Asset Name</th>
                                <th class="py-3 font-weight-normal border-0">Origin Market</th>
                                <th class="py-3 font-weight-normal border-0 text-end">Current Price</th>
                                <th class="py-3 font-weight-normal border-0 text-center">24h Trend</th>
                                <th class="py-3 font-weight-normal border-0 text-center">Volatility Risk</th>
                                <th class="py-3 font-weight-normal border-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commodities as $commodity)
                            <tr data-bs-toggle="modal" data-bs-target="#pricingModal" style="cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='rgba(250, 204, 21, 0.05)'" onmouseout="this.style.background='transparent'" onclick="loadGlobalPrices('{{ $commodity['name'] }}')">
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center border border-secondary border-opacity-50" style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.05);">
                                            @if(str_contains(strtolower($commodity['name']), 'oil') || str_contains(strtolower($commodity['name']), 'gas'))
                                                <span class="material-symbols-outlined fs-5 text-muted">oil_barrel</span>
                                            @elseif(str_contains(strtolower($commodity['name']), 'gold') || str_contains(strtolower($commodity['name']), 'silver') || str_contains(strtolower($commodity['name']), 'copper') || str_contains(strtolower($commodity['name']), 'iron') || str_contains(strtolower($commodity['name']), 'lithium') || str_contains(strtolower($commodity['name']), 'aluminum'))
                                                <span class="material-symbols-outlined fs-5 text-warning">monetization_on</span>
                                            @elseif(str_contains(strtolower($commodity['name']), 'wheat') || str_contains(strtolower($commodity['name']), 'corn') || str_contains(strtolower($commodity['name']), 'soybeans') || str_contains(strtolower($commodity['name']), 'coffee') || str_contains(strtolower($commodity['name']), 'cotton'))
                                                <span class="material-symbols-outlined fs-5 text-success">agriculture</span>
                                            @else
                                                <span class="material-symbols-outlined fs-5 text-info">category</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-white fw-bold">{{ $commodity['name'] }}</div>
                                            <div class="text-muted fs-8">{{ $commodity['unit'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fi fi-{{ $commodity['code'] }} rounded" style="width: 24px; height: 18px; object-fit: cover;"></span>
                                        <span class="text-white fw-medium fs-7">{{ $commodity['country'] }}</span>
                                    </div>
                                </td>
                                <td class="text-end py-3">
                                    <span class="text-white fw-bold fs-6">{{ number_format($commodity['price'], 2) }}</span>
                                </td>
                                <td class="text-center py-3">
                                    @if($commodity['trend'] > 0)
                                        <div class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill trend-up border border-success border-opacity-25 fs-7 fw-medium">
                                            <span class="material-symbols-outlined fs-6">trending_up</span> +{{ $commodity['trend'] }}%
                                        </div>
                                    @elseif($commodity['trend'] < 0)
                                        <div class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill trend-down border border-danger border-opacity-25 fs-7 fw-medium">
                                            <span class="material-symbols-outlined fs-6">trending_down</span> {{ $commodity['trend'] }}%
                                        </div>
                                    @else
                                        <div class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill trend-flat border border-secondary border-opacity-25 fs-7 fw-medium">
                                            <span class="material-symbols-outlined fs-6">trending_flat</span> 0.0%
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center py-3">
                                    @if($commodity['risk'] === 'High')
                                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">High</span>
                                    @elseif($commodity['risk'] === 'Medium')
                                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">Medium</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">Low</span>
                                    @endif
                                </td>
                                <td class="text-end py-3">
                                    <button class="btn btn-sm glass-pill text-warning hover-white" onclick="event.stopPropagation();">
                                        Analyze
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-auto custom-pagination d-flex justify-content-end pt-3 border-top border-secondary border-opacity-10">
                    {{ $commodities->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>

        <!-- Right Side Panel -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            
            <!-- AI Market Predictions -->
            <div class="glass-panel p-4 rounded-4 flex-grow-1 d-flex flex-column">
                <h5 class="text-white fw-bold mb-4 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-purple-neon">psychology</span>
                    AI Trade Recommendations
                </h5>
                
                <div class="d-flex flex-column gap-3 mb-4">
                    @foreach($recommendations as $rec)
                        @if(str_contains(strtolower($rec['type']), 'arbitrage') || str_contains(strtolower($rec['title']), 'opportunity'))
                        <div class="p-3 rounded-3 border border-success border-opacity-25" style="background: rgba(34, 197, 94, 0.05);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="material-symbols-outlined text-success fs-5">monetization_on</span>
                                    <span class="text-white fw-bold fs-7">{{ $rec['title'] }}</span>
                                </div>
                                <span class="badge bg-success text-white px-2 py-1 rounded-pill fs-8">Opportunity</span>
                            </div>
                            <p class="text-muted fs-8 mb-0">{{ $rec['description'] }}</p>
                        </div>
                        @else
                        <div class="p-3 rounded-3 border border-purple-neon border-opacity-25" style="background: rgba(139, 92, 246, 0.05);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="material-symbols-outlined text-purple-neon fs-5">swap_calls</span>
                                    <span class="text-white fw-bold fs-7">{{ $rec['title'] }}</span>
                                </div>
                                <span class="badge bg-purple-neon text-white px-2 py-1 rounded-pill fs-8">Arbitrage</span>
                            </div>
                            <p class="text-muted fs-8 mb-0">{{ $rec['description'] }}</p>
                        </div>
                        @endif
                    @endforeach
                </div>
                
                <!-- Macro Indicators -->
                <h6 class="text-muted text-uppercase fs-8 fw-bold mb-3 mt-auto">Macro Market Indicators</h6>
                <div class="d-flex flex-column gap-2 mb-4">
                    <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background: rgba(255,255,255,0.02);">
                        <span class="text-muted fs-7">Global Supply Index</span>
                        <span class="text-success fw-bold fs-7">Optimal (84%)</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background: rgba(255,255,255,0.02);">
                        <span class="text-muted fs-7">Freight Cost Index</span>
                        <span class="text-danger fw-bold fs-7">Elevated (+4.2%)</span>
                    </div>
                </div>

                <button class="btn btn-outline-warning w-100 rounded-pill mt-auto d-flex justify-content-center align-items-center gap-2 transition-all">
                    <span class="material-symbols-outlined fs-6">radar</span> Scan Arbitrage Opportunities
                </button>
            </div>
            
        </div>
    </div>
</main>

<!-- Global Pricing Modal -->
<div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-panel border-0 shadow-lg" style="background: rgba(10, 17, 40, 0.95); backdrop-filter: blur(24px);">
            <div class="modal-header border-bottom border-secondary border-opacity-25">
                <h5 class="modal-title text-white fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-warning">public</span>
                    Global Market Prices: <span id="modalCommodityName" class="text-warning"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="modalLoading" class="p-5 text-center text-white">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="mt-3 mb-0 text-muted">Scanning global exchanges...</p>
                </div>
                <div class="table-responsive" style="max-height: 450px; display: none;" id="modalContent">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead class="position-sticky top-0 z-1" style="background: var(--glass-bg); backdrop-filter: blur(24px);">
                            <tr>
                                <th class="py-3 border-secondary border-opacity-25 text-muted fw-normal text-uppercase fs-8">Country Market</th>
                                <th class="py-3 border-secondary border-opacity-25 text-muted fw-normal text-uppercase fs-8 text-end">Local Price</th>
                                <th class="py-3 border-secondary border-opacity-25 text-muted fw-normal text-uppercase fs-8 text-center">Supply Trend</th>
                                <th class="py-3 border-secondary border-opacity-25 text-muted fw-normal text-uppercase fs-8 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="pricingTableBody">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadGlobalPrices(commodityName) {
    document.getElementById('modalCommodityName').textContent = commodityName;
    document.getElementById('modalLoading').style.display = 'block';
    document.getElementById('modalContent').style.display = 'none';
    
    // Fetch data
    fetch(`/intelligence/commodities/${encodeURIComponent(commodityName)}/prices`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const tbody = document.getElementById('pricingTableBody');
                tbody.innerHTML = '';
                
                data.data.forEach(item => {
                    // Trend badge
                    let trendBadge = '';
                    if(item.trend > 0) trendBadge = `<div class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill trend-up border border-success border-opacity-25 fs-8 fw-medium"><span class="material-symbols-outlined fs-7 align-middle">trending_up</span> +${item.trend}%</div>`;
                    else if(item.trend < 0) trendBadge = `<div class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill trend-down border border-danger border-opacity-25 fs-8 fw-medium"><span class="material-symbols-outlined fs-7 align-middle">trending_down</span> ${item.trend}%</div>`;
                    else trendBadge = `<div class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill trend-flat border border-secondary border-opacity-25 fs-8 fw-medium"><span class="material-symbols-outlined fs-7 align-middle">trending_flat</span> 0.0%</div>`;
                    
                    // Status badge
                    let statusBadge = '';
                    if(item.status === 'Premium') statusBadge = `<span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1">Premium</span>`;
                    else if(item.status === 'Discount') statusBadge = `<span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2 py-1">Discount</span>`;
                    else statusBadge = `<span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25 rounded-pill px-2 py-1">Market Rate</span>`;
                    
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="py-3 ps-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fi fi-${item.code} rounded shadow-sm" style="width: 24px; height: 18px; object-fit: cover;"></span>
                                <span class="text-white fw-medium fs-7">${item.country}</span>
                            </div>
                        </td>
                        <td class="py-3 text-end text-white fw-bold fs-6">${item.price.toFixed(2)} <span class="text-muted fs-8 fw-normal">${item.unit}</span></td>
                        <td class="py-3 text-center">${trendBadge}</td>
                        <td class="py-3 text-center">${statusBadge}</td>
                    `;
                    tbody.appendChild(tr);
                });
                
                document.getElementById('modalLoading').style.display = 'none';
                document.getElementById('modalContent').style.display = 'block';
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('modalLoading').innerHTML = '<div class="text-danger p-4"><span class="material-symbols-outlined fs-1">error</span><p>Failed to fetch global market data.</p></div>';
        });
}
</script>
@endsection
