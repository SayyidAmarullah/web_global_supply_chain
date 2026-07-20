@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>
<style>
.glass-panel {
    background: rgba(10, 17, 40, 0.4);
    backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.vs-badge {
    width: 60px;
    height: 60px;
    background: var(--purple-neon);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 1.5rem;
    box-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
    z-index: 10;
}
.compare-row {
    position: relative;
}
.compare-item {
    transition: all 0.3s ease;
}
.compare-item:hover {
    background: rgba(255, 255, 255, 0.05);
}
.winner {
    background: rgba(16, 185, 129, 0.1) !important;
}
.winner h3 {
    color: var(--success) !important;
}
.loser {
    opacity: 0.6;
}

/* Glowing borders */
.glow-info {
    border: 2px solid #0dcaf0 !important;
    box-shadow: 0 0 20px rgba(13, 202, 240, 0.6), inset 0 0 10px rgba(13, 202, 240, 0.2);
}
.glow-warning {
    border: 2px solid #ffc107 !important;
    box-shadow: 0 0 20px rgba(255, 193, 7, 0.6), inset 0 0 10px rgba(255, 193, 7, 0.2);
}

/* Light Mode Overrides */
:root[data-theme="light"] .glass-panel {
    border: 1px solid rgba(0,0,0,0.05) !important;
    background: rgba(255, 255, 255, 0.8) !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}
:root[data-theme="light"] .text-white { color: #111827 !important; }
:root[data-theme="light"] .text-muted { color: #6b7280 !important; }
</style>

<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                Commodity Comparison Engine
            </h2>
            <p class="text-muted mb-0 fs-7">Compare Price, Trend, and Supply Chain Risk</p>
        </div>
        <div>
            <button class="btn btn-primary rounded-pill px-4" onclick="runComparison()">
                <span class="material-symbols-outlined fs-6 align-middle">play_arrow</span> Compare Now
            </button>
        </div>
    </header>

    <!-- Selection Row -->
    <div class="row g-4 mb-2 position-relative align-items-center">
        <!-- Commodity A -->
        <div class="col-md-5">
            <div class="glass-panel p-4 rounded-4 glow-info">
                <label class="text-muted fs-8 text-uppercase fw-bold mb-2">Commodity A</label>
                <select id="commodityA" class="form-select bg-dark border-secondary text-white py-3 shadow-none">
                    @foreach($commodities as $index => $commodity)
                        <option value="{{ $index }}" {{ $index === 0 ? 'selected' : '' }}>{{ $commodity['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <!-- VS Badge -->
        <div class="col-md-2 d-flex justify-content-center">
            <div class="vs-badge position-absolute top-50 start-50 translate-middle">VS</div>
        </div>

        <!-- Commodity B -->
        <div class="col-md-5">
            <div class="glass-panel p-4 rounded-4 glow-warning">
                <label class="text-muted fs-8 text-uppercase fw-bold mb-2">Commodity B</label>
                <select id="commodityB" class="form-select bg-dark border-secondary text-white py-3 shadow-none">
                    @foreach($commodities as $index => $commodity)
                        <option value="{{ $index }}" {{ $index === 1 ? 'selected' : '' }}>{{ $commodity['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Comparison Results -->
    <div id="loadingState" class="glass-panel p-5 rounded-4 text-center mt-4 d-none">
        <div class="spinner-border text-purple-neon mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
        <h4 class="text-white fw-bold">Analyzing Markets...</h4>
        <p class="text-muted">Fetching live prices, market trends, and risk assessments.</p>
    </div>

    <div id="resultsContainer" class="d-none flex-column gap-3 mt-2">
        
        <!-- Price Compare -->
        <div class="glass-panel rounded-4 overflow-hidden border border-secondary border-opacity-25">
            <div class="bg-dark bg-opacity-50 p-2 text-center border-bottom border-secondary border-opacity-25">
                <span class="text-muted fs-8 text-uppercase fw-bold d-flex align-items-center justify-content-center gap-2">
                    <span class="material-symbols-outlined fs-6 text-success">payments</span> Current Market Price
                </span>
            </div>
            <div class="row g-0">
                <div class="col-6 p-4 text-center compare-item border-end border-secondary border-opacity-25" id="priceA">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                    <p class="text-muted fs-8 mb-0">...</p>
                </div>
                <div class="col-6 p-4 text-center compare-item" id="priceB">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                    <p class="text-muted fs-8 mb-0">...</p>
                </div>
            </div>
        </div>

        <!-- Trend Compare -->
        <div class="glass-panel rounded-4 overflow-hidden border border-secondary border-opacity-25">
            <div class="bg-dark bg-opacity-50 p-2 text-center border-bottom border-secondary border-opacity-25">
                <span class="text-muted fs-8 text-uppercase fw-bold d-flex align-items-center justify-content-center gap-2">
                    <span class="material-symbols-outlined fs-6 text-info">trending_up</span> Market Trend (24h)
                </span>
            </div>
            <div class="row g-0">
                <div class="col-6 p-4 text-center compare-item border-end border-secondary border-opacity-25" id="trendA">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                </div>
                <div class="col-6 p-4 text-center compare-item" id="trendB">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                </div>
            </div>
        </div>

        <!-- Risk Score Compare -->
        <div class="glass-panel rounded-4 overflow-hidden border border-secondary border-opacity-25">
            <div class="bg-dark bg-opacity-50 p-2 text-center border-bottom border-secondary border-opacity-25">
                <span class="text-muted fs-8 text-uppercase fw-bold d-flex align-items-center justify-content-center gap-2">
                    <span class="material-symbols-outlined fs-6 text-danger">warning</span> Supply Chain Risk
                </span>
            </div>
            <div class="row g-0">
                <div class="col-6 p-4 text-center compare-item border-end border-secondary border-opacity-25" id="riskA">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                </div>
                <div class="col-6 p-4 text-center compare-item" id="riskB">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                </div>
            </div>
        </div>

        <!-- Producer Compare -->
        <div class="glass-panel rounded-4 overflow-hidden border border-secondary border-opacity-25">
            <div class="bg-dark bg-opacity-50 p-2 text-center border-bottom border-secondary border-opacity-25">
                <span class="text-muted fs-8 text-uppercase fw-bold d-flex align-items-center justify-content-center gap-2">
                    <span class="material-symbols-outlined fs-6 text-warning">public</span> Major Producer
                </span>
            </div>
            <div class="row g-0">
                <div class="col-6 p-4 text-center compare-item border-end border-secondary border-opacity-25" id="prodA">
                    <span id="flagA" class="fi rounded shadow-sm mb-2" style="width: 50px; height: 35px; background-size: cover; background-position: center; display: inline-block;"></span>
                    <h5 class="text-white fw-bold mb-0">...</h5>
                </div>
                <div class="col-6 p-4 text-center compare-item" id="prodB">
                    <span id="flagB" class="fi rounded shadow-sm mb-2" style="width: 50px; height: 35px; background-size: cover; background-position: center; display: inline-block;"></span>
                    <h5 class="text-white fw-bold mb-0">...</h5>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
const commoditiesData = {!! json_encode($commodities) !!};

document.addEventListener('DOMContentLoaded', () => {
    // Auto-run if elements exist
    runComparison();
});

function runComparison() {
    const indexA = document.getElementById('commodityA').value;
    const indexB = document.getElementById('commodityB').value;

    if(indexA === '' || indexB === '') return;

    // Show Loading
    document.getElementById('resultsContainer').classList.add('d-none');
    document.getElementById('loadingState').classList.remove('d-none');

    setTimeout(() => {
        const cA = commoditiesData[indexA];
        const cB = commoditiesData[indexB];

        // Display Price
        document.querySelector('#priceA h3').textContent = `$${cA.price.toFixed(2)}`;
        document.querySelector('#priceA p').textContent = `per ${cA.unit.split('/')[1]}`;
        document.querySelector('#priceB h3').textContent = `$${cB.price.toFixed(2)}`;
        document.querySelector('#priceB p').textContent = `per ${cB.unit.split('/')[1]}`;
        // Usually lower price is better for buyers, higher for sellers, let's not highlight price

        // Display Trend
        document.querySelector('#trendA h3').innerHTML = formatTrend(cA.trend);
        document.querySelector('#trendB h3').innerHTML = formatTrend(cB.trend);
        applyWinner('#trendA', '#trendB', cA.trend > cB.trend); // higher trend is "winner" for display purposes

        // Display Risk
        document.querySelector('#riskA h3').innerHTML = formatRisk(cA.risk);
        document.querySelector('#riskB h3').innerHTML = formatRisk(cB.risk);
        const riskLevel = { 'Low': 1, 'Medium': 2, 'High': 3 };
        applyWinner('#riskA', '#riskB', riskLevel[cA.risk] < riskLevel[cB.risk]); // lower risk is better

        // Display Producer
        document.querySelector('#prodA h5').textContent = cA.country;
        document.querySelector('#prodA #flagA').className = `fi fi-${cA.code} rounded shadow-sm mb-2`;
        document.querySelector('#prodB h5').textContent = cB.country;
        document.querySelector('#prodB #flagB').className = `fi fi-${cB.code} rounded shadow-sm mb-2`;

        // Hide Loading, Show Results
        document.getElementById('loadingState').classList.add('d-none');
        document.getElementById('resultsContainer').classList.remove('d-none');
        document.getElementById('resultsContainer').style.display = 'flex';
    }, 800); // simulate API delay
}

function formatTrend(trend) {
    if (trend > 0) return `<span class="text-success">+${trend}% <i class="material-symbols-outlined align-middle fs-5">arrow_upward</i></span>`;
    if (trend < 0) return `<span class="text-danger">${trend}% <i class="material-symbols-outlined align-middle fs-5">arrow_downward</i></span>`;
    return `<span class="text-muted">0%</span>`;
}

function formatRisk(risk) {
    if (risk === 'Low') return `<span class="text-success">Low Risk</span>`;
    if (risk === 'Medium') return `<span class="text-warning">Medium Risk</span>`;
    return `<span class="text-danger">High Risk</span>`;
}

function applyWinner(idA, idB, isAWinner) {
    document.querySelector(idA).classList.remove('winner', 'loser');
    document.querySelector(idB).classList.remove('winner', 'loser');
    if (isAWinner) {
        document.querySelector(idA).classList.add('winner');
        document.querySelector(idB).classList.add('loser');
    } else {
        document.querySelector(idB).classList.add('winner');
        document.querySelector(idA).classList.add('loser');
    }
}
</script>
@endsection
