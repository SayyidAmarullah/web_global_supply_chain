@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>
<style>
.glass-panel {
    background: rgba(10, 17, 40, 0.4);
    backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.kpi-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.country-card {
    cursor: pointer;
    transition: all 0.3s ease;
    background: rgba(255,255,255,0.02);
}
.country-card:hover {
    background: rgba(255,255,255,0.08);
    transform: translateY(-5px);
    border-color: rgba(255,255,255,0.2) !important;
}
.text-glow {
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
}

/* Light Mode Overrides */
:root[data-theme="light"] .glass-panel {
    border: 1px solid rgba(0,0,0,0.05) !important;
    background: rgba(255, 255, 255, 0.8) !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}
:root[data-theme="light"] .country-card {
    background: rgba(0,0,0,0.02);
}
:root[data-theme="light"] .country-card:hover {
    background: rgba(0,0,0,0.05);
    border-color: rgba(0,0,0,0.1) !important;
}
:root[data-theme="light"] .text-white { color: #111827 !important; }
:root[data-theme="light"] .text-muted { color: #6b7280 !important; }
:root[data-theme="light"] .text-glow { text-shadow: none; }
</style>

<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <span class="material-symbols-outlined fs-2 text-primary">account_balance</span>
                Global Macroeconomics
            </h2>
            <p class="text-muted mb-0 fs-7">Select an economy to view live World Bank indicators</p>
        </div>
        <div id="headerActions" style="display: none;">
            <button class="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-2" onclick="showCountryGrid()">
                <span class="material-symbols-outlined fs-6">arrow_back</span> Back to Countries
            </button>
        </div>
    </header>

    <!-- State 1: Country Selection Grid -->
    <div id="countryGridContainer" class="flex-grow-1">
        <div class="mb-4">
            <div class="input-group" style="max-width: 400px;">
                <span class="input-group-text bg-dark border-secondary text-muted">
                    <span class="material-symbols-outlined fs-5">search</span>
                </span>
                <input type="text" id="countrySearch" class="form-control bg-dark text-white border-secondary" placeholder="Search countries..." onkeyup="filterCountries()">
            </div>
        </div>

        <div class="row g-3" id="countriesRow">
            @foreach($countries as $c)
            <div class="col-md-4 col-lg-3 country-item" data-name="{{ strtolower($c['name']) }}" data-region="{{ strtolower($c['region']) }}">
                <div class="glass-panel p-3 rounded-4 h-100 country-card border border-secondary border-opacity-25" onclick="fetchMacroData('{{ $c['code'] }}', '{{ $c['name'] }}')">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fi fi-{{ strtolower($c['code']) }} rounded shadow-sm" style="width: 32px; height: 24px; object-fit: cover;"></span>
                        <div class="text-truncate">
                            <h6 class="text-white fw-bold mb-0 text-truncate">{{ $c['name'] }}</h6>
                            <span class="text-muted" style="font-size: 0.7rem;">{{ $c['region'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- State 2: Loading Overlay -->
    <div id="loadingOverlay" class="d-flex justify-content-center align-items-center flex-grow-1" style="display: none !important;">
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
            <h5 class="text-white">Connecting to World Bank Database...</h5>
            <p class="text-muted">Fetching latest economic indicators</p>
        </div>
    </div>

    <!-- State 3: Data Container -->
    <div id="dataContainer" class="d-flex flex-column gap-4 flex-grow-1" style="display: none !important;">
        <!-- Country Overview Card -->
        <div class="glass-panel p-4 rounded-4 position-relative overflow-hidden">
            <div class="position-absolute top-50 translate-middle-y end-0 pe-4 opacity-10">
                <span class="material-symbols-outlined" style="font-size: 150px;">public</span>
            </div>
            
            <div class="d-flex align-items-center gap-4 position-relative z-1">
                <div id="flagContainer" class="rounded shadow-lg" style="width: 100px; height: 75px; background-size: cover; background-position: center; border: 2px solid rgba(255,255,255,0.1);"></div>
                <div>
                    <h1 id="countryNameDisplay" class="text-white fw-bold mb-1 text-glow">Country</h1>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fs-7 d-flex align-items-center gap-1">
                            <span class="material-symbols-outlined fs-6">verified</span> World Bank Verified
                        </span>
                        <span id="dataYearDisplay" class="text-muted fs-7">Latest Available Data: ...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Grid -->
        <div class="row g-4 mb-2">
            <!-- GDP -->
            <div class="col-md-6 col-lg-4">
                <div class="glass-panel p-4 rounded-4 h-100 kpi-card border-top border-4 border-success">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-success bg-opacity-10 rounded-3">
                                <span class="material-symbols-outlined text-success fs-4">account_balance_wallet</span>
                            </div>
                            <h6 class="text-muted text-uppercase fw-bold fs-8 mb-0 tracking-wide">Gross Domestic Product (GDP)</h6>
                        </div>
                    </div>
                    <h2 id="gdpVal" class="text-white fw-bold mb-1">Loading...</h2>
                    <p class="text-muted fs-8 mb-0">Current US$</p>
                </div>
            </div>

            <!-- Inflation -->
            <div class="col-md-6 col-lg-4">
                <div class="glass-panel p-4 rounded-4 h-100 kpi-card border-top border-4 border-danger">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-danger bg-opacity-10 rounded-3">
                                <span class="material-symbols-outlined text-danger fs-4">trending_up</span>
                            </div>
                            <h6 class="text-muted text-uppercase fw-bold fs-8 mb-0 tracking-wide">Inflation Rate</h6>
                        </div>
                    </div>
                    <h2 id="inflationVal" class="text-white fw-bold mb-1">Loading...</h2>
                    <p class="text-muted fs-8 mb-0">Consumer Prices (Annual %)</p>
                </div>
            </div>

            <!-- Population -->
            <div class="col-md-6 col-lg-4">
                <div class="glass-panel p-4 rounded-4 h-100 kpi-card border-top border-4 border-info">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-info bg-opacity-10 rounded-3">
                                <span class="material-symbols-outlined text-info fs-4">groups</span>
                            </div>
                            <h6 class="text-muted text-uppercase fw-bold fs-8 mb-0 tracking-wide">Total Population</h6>
                        </div>
                    </div>
                    <h2 id="populationVal" class="text-white fw-bold mb-1">Loading...</h2>
                    <p class="text-muted fs-8 mb-0">Total Demographics</p>
                </div>
            </div>

            <!-- Exports -->
            <div class="col-md-6">
                <div class="glass-panel p-4 rounded-4 h-100 kpi-card border-start border-4 border-warning">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-warning bg-opacity-10 rounded-3">
                                <span class="material-symbols-outlined text-warning fs-4">flight_takeoff</span>
                            </div>
                            <h6 class="text-muted text-uppercase fw-bold fs-8 mb-0 tracking-wide">Total Exports</h6>
                        </div>
                    </div>
                    <h2 id="exportVal" class="text-white fw-bold mb-1">Loading...</h2>
                    <p class="text-muted fs-8 mb-0">Goods and Services (Current US$)</p>
                </div>
            </div>

            <!-- Imports -->
            <div class="col-md-6">
                <div class="glass-panel p-4 rounded-4 h-100 kpi-card border-start border-4 border-primary">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-primary bg-opacity-10 rounded-3">
                                <span class="material-symbols-outlined text-primary fs-4">flight_land</span>
                            </div>
                            <h6 class="text-muted text-uppercase fw-bold fs-8 mb-0 tracking-wide">Total Imports</h6>
                        </div>
                    </div>
                    <h2 id="importVal" class="text-white fw-bold mb-1">Loading...</h2>
                    <p class="text-muted fs-8 mb-0">Goods and Services (Current US$)</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function formatCurrency(num) {
    if(!num || num === 'N/A') return 'N/A';
    if(num >= 1e12) return '$' + (num / 1e12).toFixed(2) + ' Trillion';
    if(num >= 1e9) return '$' + (num / 1e9).toFixed(2) + ' Billion';
    if(num >= 1e6) return '$' + (num / 1e6).toFixed(2) + ' Million';
    return '$' + num.toLocaleString();
}

function formatNumber(num) {
    if(!num || num === 'N/A') return 'N/A';
    if(num >= 1e9) return (num / 1e9).toFixed(2) + ' Billion';
    if(num >= 1e6) return (num / 1e6).toFixed(2) + ' Million';
    return num.toLocaleString();
}

function formatPercent(num) {
    if(!num || num === 'N/A') return 'N/A';
    return parseFloat(num).toFixed(2) + '%';
}

function showCountryGrid() {
    document.getElementById('dataContainer').style.setProperty('display', 'none', 'important');
    document.getElementById('loadingOverlay').style.setProperty('display', 'none', 'important');
    document.getElementById('headerActions').style.display = 'none';
    document.getElementById('countryGridContainer').style.display = 'block';
}

function filterCountries() {
    const query = document.getElementById('countrySearch').value.toLowerCase();
    const items = document.querySelectorAll('.country-item');
    
    items.forEach(item => {
        const name = item.getAttribute('data-name');
        const region = item.getAttribute('data-region');
        
        if (name.includes(query) || region.includes(query)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

async function fetchMacroData(countryCode, countryName) {
    // Hide grid, show loading
    document.getElementById('countryGridContainer').style.display = 'none';
    document.getElementById('loadingOverlay').style.setProperty('display', 'flex', 'important');
    
    // Set flag and name
    document.getElementById('countryNameDisplay').textContent = countryName;
    const flagClass = `fi-${countryCode.toLowerCase()}`;
    document.getElementById('flagContainer').className = `rounded shadow-lg fi ${flagClass}`;
    
    const endpoints = {
        gdp: 'NY.GDP.MKTP.CD',
        inflation: 'FP.CPI.TOTL.ZG',
        population: 'SP.POP.TOTL',
        export: 'NE.EXP.GNFS.CD',
        import: 'NE.IMP.GNFS.CD'
    };

    try {
        let latestYear = 'N/A';
        
        for (const [key, indicator] of Object.entries(endpoints)) {
            const res = await fetch(`https://api.worldbank.org/v2/country/${countryCode}/indicator/${indicator}?format=json&per_page=5`);
            const data = await res.json();
            
            let value = 'N/A';
            if (data && data[1]) {
                const validEntry = data[1].find(item => item.value !== null);
                if (validEntry) {
                    value = validEntry.value;
                    latestYear = validEntry.date;
                }
            }
            
            if (key === 'gdp' || key === 'export' || key === 'import') {
                document.getElementById(`${key}Val`).textContent = formatCurrency(value);
            } else if (key === 'inflation') {
                document.getElementById(`${key}Val`).textContent = formatPercent(value);
                const el = document.getElementById(`${key}Val`);
                if(value !== 'N/A') {
                    el.className = parseFloat(value) > 5 ? 'text-danger fw-bold mb-1' : 'text-white fw-bold mb-1';
                }
            } else if (key === 'population') {
                document.getElementById(`${key}Val`).textContent = formatNumber(value);
            }
        }
        
        document.getElementById('dataYearDisplay').textContent = `Latest Available Data: ${latestYear}`;
        
    } catch (e) {
        console.error("Failed to fetch World Bank Data:", e);
    } finally {
        document.getElementById('loadingOverlay').style.setProperty('display', 'none', 'important');
        document.getElementById('dataContainer').style.setProperty('display', 'flex', 'important');
        document.getElementById('headerActions').style.display = 'block';
    }
}
</script>
@endsection
