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
                <span class="material-symbols-outlined fs-2 text-purple-neon">compare_arrows</span>
                Country Comparison Engine
            </h2>
            <p class="text-muted mb-0 fs-7">Compare Macroeconomics, Risk, Weather, and Currency</p>
        </div>
        <div>
            <button class="btn btn-primary rounded-pill px-4" onclick="runComparison()">
                <span class="material-symbols-outlined fs-6 align-middle">play_arrow</span> Compare Now
            </button>
        </div>
    </header>

    <!-- Selection Row -->
    <div class="row g-4 mb-2 position-relative align-items-center">
        <!-- Country A -->
        <div class="col-md-5">
            <div class="glass-panel p-4 rounded-4 glow-info">
                <label class="text-muted fs-8 text-uppercase fw-bold mb-2">Country A</label>
                <select id="countryA" class="form-select bg-dark border-secondary text-white py-3 shadow-none">
                    @foreach($countries as $country)
                        <option value="{{ $country['code'] }}" {{ $country['code'] === 'DE' ? 'selected' : '' }}>{{ $country['name'] }}</option>
                    @endforeach
                </select>
                <div id="flagA" class="mt-3 text-center d-none">
                    <span class="fi fi-de rounded shadow-sm" style="width: 120px; height: 90px; background-size: cover; background-position: center; display: inline-block;"></span>
                </div>
            </div>
        </div>
        
        <!-- VS Badge -->
        <div class="col-md-2 d-flex justify-content-center">
            <div class="vs-badge position-absolute top-50 start-50 translate-middle">VS</div>
        </div>

        <!-- Country B -->
        <div class="col-md-5">
            <div class="glass-panel p-4 rounded-4 glow-warning">
                <label class="text-muted fs-8 text-uppercase fw-bold mb-2">Country B</label>
                <select id="countryB" class="form-select bg-dark border-secondary text-white py-3 shadow-none">
                    @foreach($countries as $country)
                        <option value="{{ $country['code'] }}" {{ $country['code'] === 'AU' ? 'selected' : '' }}>{{ $country['name'] }}</option>
                    @endforeach
                </select>
                <div id="flagB" class="mt-3 text-center d-none">
                    <span class="fi fi-au rounded shadow-sm" style="width: 120px; height: 90px; background-size: cover; background-position: center; display: inline-block;"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparison Results -->
    <div id="loadingState" class="glass-panel p-5 rounded-4 text-center mt-4 d-none">
        <div class="spinner-border text-purple-neon mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
        <h4 class="text-white fw-bold">Gathering Intelligence...</h4>
        <p class="text-muted">Fetching live GDP, Inflation, Weather, and Currency metrics.</p>
    </div>

    <div id="resultsContainer" class="d-none flex-column gap-3 mt-2">
        
        <!-- GDP Compare -->
        <div class="glass-panel rounded-4 overflow-hidden border border-secondary border-opacity-25">
            <div class="bg-dark bg-opacity-50 p-2 text-center border-bottom border-secondary border-opacity-25">
                <span class="text-muted fs-8 text-uppercase fw-bold d-flex align-items-center justify-content-center gap-2">
                    <span class="material-symbols-outlined fs-6 text-cyan-glow">account_balance</span> Gross Domestic Product (GDP)
                </span>
            </div>
            <div class="row g-0">
                <div class="col-6 p-4 text-center compare-item border-end border-secondary border-opacity-25" id="gdpA">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                </div>
                <div class="col-6 p-4 text-center compare-item" id="gdpB">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                </div>
            </div>
        </div>

        <!-- Inflation Compare -->
        <div class="glass-panel rounded-4 overflow-hidden border border-secondary border-opacity-25">
            <div class="bg-dark bg-opacity-50 p-2 text-center border-bottom border-secondary border-opacity-25">
                <span class="text-muted fs-8 text-uppercase fw-bold d-flex align-items-center justify-content-center gap-2">
                    <span class="material-symbols-outlined fs-6 text-danger">trending_up</span> Inflation Rate
                </span>
            </div>
            <div class="row g-0">
                <div class="col-6 p-4 text-center compare-item border-end border-secondary border-opacity-25" id="infA">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                </div>
                <div class="col-6 p-4 text-center compare-item" id="infB">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                </div>
            </div>
        </div>

        <!-- Risk Score Compare -->
        <div class="glass-panel rounded-4 overflow-hidden border border-secondary border-opacity-25">
            <div class="bg-dark bg-opacity-50 p-2 text-center border-bottom border-secondary border-opacity-25">
                <span class="text-muted fs-8 text-uppercase fw-bold d-flex align-items-center justify-content-center gap-2">
                    <span class="material-symbols-outlined fs-6 text-warning">warning</span> Supply Chain Risk Score
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

        <!-- Weather Compare -->
        <div class="glass-panel rounded-4 overflow-hidden border border-secondary border-opacity-25">
            <div class="bg-dark bg-opacity-50 p-2 text-center border-bottom border-secondary border-opacity-25">
                <span class="text-muted fs-8 text-uppercase fw-bold d-flex align-items-center justify-content-center gap-2">
                    <span class="material-symbols-outlined fs-6 text-info">cloud</span> Live Weather (Capital City)
                </span>
            </div>
            <div class="row g-0">
                <div class="col-6 p-4 text-center compare-item border-end border-secondary border-opacity-25" id="weatherA">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                    <p class="text-muted fs-8 mb-0">...</p>
                </div>
                <div class="col-6 p-4 text-center compare-item" id="weatherB">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                    <p class="text-muted fs-8 mb-0">...</p>
                </div>
            </div>
        </div>

        <!-- Currency Compare -->
        <div class="glass-panel rounded-4 overflow-hidden border border-secondary border-opacity-25">
            <div class="bg-dark bg-opacity-50 p-2 text-center border-bottom border-secondary border-opacity-25">
                <span class="text-muted fs-8 text-uppercase fw-bold d-flex align-items-center justify-content-center gap-2">
                    <span class="material-symbols-outlined fs-6 text-success">payments</span> Currency Exchange (vs USD)
                </span>
            </div>
            <div class="row g-0">
                <div class="col-6 p-4 text-center compare-item border-end border-secondary border-opacity-25" id="currA">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                    <p class="text-muted fs-8 mb-0">...</p>
                </div>
                <div class="col-6 p-4 text-center compare-item" id="currB">
                    <h3 class="text-white fw-bold mb-0">...</h3>
                    <p class="text-muted fs-8 mb-0">...</p>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
let allCountriesData = [];

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('https://cdn.jsdelivr.net/gh/mledoze/countries@master/countries.json');
        allCountriesData = await response.json();
        
        // Auto-run if elements exist
        runComparison();
    } catch (e) {
        console.error("Failed to load country DB", e);
    }
});

async function runComparison() {
    const codeA = document.getElementById('countryA').value;
    const codeB = document.getElementById('countryB').value;

    if(!codeA || !codeB) return;

    // Show Flags
    const flagA = document.getElementById('flagA');
    const flagB = document.getElementById('flagB');
    flagA.classList.remove('d-none');
    flagB.classList.remove('d-none');
    flagA.innerHTML = `<span class="fi fi-${codeA.toLowerCase()} rounded shadow-sm" style="width: 100px; height: 75px; background-size: cover; background-position: center; display: inline-block;"></span>`;
    flagB.innerHTML = `<span class="fi fi-${codeB.toLowerCase()} rounded shadow-sm" style="width: 100px; height: 75px; background-size: cover; background-position: center; display: inline-block;"></span>`;

    // Show Loading
    document.getElementById('resultsContainer').classList.add('d-none');
    document.getElementById('loadingState').classList.remove('d-none');

    // Get country info from local DB
    const cA = allCountriesData.find(c => c.cca2 === codeA);
    const cB = allCountriesData.find(c => c.cca2 === codeB);

    // Fetch World Bank Data
    const gdpDataA = await fetchWBData(codeA, 'NY.GDP.MKTP.CD');
    const gdpDataB = await fetchWBData(codeB, 'NY.GDP.MKTP.CD');
    const infDataA = await fetchWBData(codeA, 'FP.CPI.TOTL.ZG');
    const infDataB = await fetchWBData(codeB, 'FP.CPI.TOTL.ZG');

    // Display GDP
    const gdpValA = gdpDataA !== 'N/A' ? parseFloat(gdpDataA) : 0;
    const gdpValB = gdpDataB !== 'N/A' ? parseFloat(gdpDataB) : 0;
    document.querySelector('#gdpA h3').textContent = formatCurrency(gdpDataA);
    document.querySelector('#gdpB h3').textContent = formatCurrency(gdpDataB);
    applyWinner('#gdpA', '#gdpB', gdpValA > gdpValB);

    // Display Inflation
    const infValA = infDataA !== 'N/A' ? parseFloat(infDataA) : 999; // higher is worse
    const infValB = infDataB !== 'N/A' ? parseFloat(infDataB) : 999;
    document.querySelector('#infA h3').textContent = infDataA !== 'N/A' ? infValA.toFixed(2) + '%' : 'N/A';
    document.querySelector('#infB h3').textContent = infDataB !== 'N/A' ? infValB.toFixed(2) + '%' : 'N/A';
    applyWinner('#infA', '#infB', infValA < infValB);

    // Simulated Risk Score (Random based on string hash for consistency)
    const riskA = calculateSimulatedRisk(codeA);
    const riskB = calculateSimulatedRisk(codeB);
    document.querySelector('#riskA h3').textContent = riskA + ' / 100';
    document.querySelector('#riskB h3').textContent = riskB + ' / 100';
    applyWinner('#riskA', '#riskB', riskA < riskB); // Lower risk is better

    // Weather Data (fetch from capital latlng)
    await processWeather(cA, '#weatherA');
    await processWeather(cB, '#weatherB');

    // Currency Data (ExchangeRate API)
    let currAStr = cA ? Object.keys(cA.currencies || {})[0] : null;
    let currBStr = cB ? Object.keys(cB.currencies || {})[0] : null;
    await processCurrency(currAStr, '#currA');
    await processCurrency(currBStr, '#currB');

    // Hide Loading, Show Results
    document.getElementById('loadingState').classList.add('d-none');
    document.getElementById('resultsContainer').classList.remove('d-none');
    document.getElementById('resultsContainer').style.display = 'flex';
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

async function fetchWBData(code, indicator) {
    try {
        const res = await fetch(`https://api.worldbank.org/v2/country/${code}/indicator/${indicator}?format=json&per_page=5`);
        const data = await res.json();
        if (data && data[1]) {
            const valid = data[1].find(item => item.value !== null);
            if (valid) return valid.value;
        }
    } catch(e) {}
    return 'N/A';
}

function formatCurrency(num) {
    if(num === 'N/A') return 'N/A';
    if(num >= 1e12) return '$' + (num / 1e12).toFixed(2) + ' T';
    if(num >= 1e9) return '$' + (num / 1e9).toFixed(2) + ' B';
    if(num >= 1e6) return '$' + (num / 1e6).toFixed(2) + ' M';
    return '$' + parseFloat(num).toLocaleString();
}

function calculateSimulatedRisk(code) {
    // Generates a pseudo-random number between 15 and 85 based on country code
    let hash = 0;
    for (let i = 0; i < code.length; i++) hash = code.charCodeAt(i) + ((hash << 5) - hash);
    return 15 + (Math.abs(hash) % 70);
}

async function processWeather(country, id) {
    let lat = 0, lng = 0;
    if (country && country.capitalInfo && country.capitalInfo.latlng) {
        lat = country.capitalInfo.latlng[0];
        lng = country.capitalInfo.latlng[1];
    }
    
    if (lat === 0 && lng === 0) {
        document.querySelector(`${id} h3`).textContent = 'N/A';
        document.querySelector(`${id} p`).textContent = 'No Coordinates';
        return;
    }

    try {
        const mRes = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current=temperature_2m,wind_speed_10m`);
        const mData = await mRes.json();
        if (mData.current) {
            document.querySelector(`${id} h3`).textContent = `${mData.current.temperature_2m}°C`;
            document.querySelector(`${id} p`).textContent = `Wind: ${mData.current.wind_speed_10m} km/h`;
        } else {
            throw new Error();
        }
    } catch(e) {
        document.querySelector(`${id} h3`).textContent = 'N/A';
        document.querySelector(`${id} p`).textContent = 'API Error';
    }
}

async function processCurrency(currStr, id) {
    if (!currStr) {
        document.querySelector(`${id} h3`).textContent = 'N/A';
        document.querySelector(`${id} p`).textContent = 'No Currency';
        return;
    }

    try {
        const res = await fetch(`https://open.er-api.com/v6/latest/USD`);
        const data = await res.json();
        if (data && data.rates && data.rates[currStr]) {
            document.querySelector(`${id} h3`).textContent = data.rates[currStr].toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 4});
            document.querySelector(`${id} p`).textContent = `1 USD = ${currStr}`;
        } else {
            throw new Error();
        }
    } catch (e) {
        document.querySelector(`${id} h3`).textContent = 'N/A';
        document.querySelector(`${id} p`).textContent = currStr;
    }
}
</script>
@endsection
