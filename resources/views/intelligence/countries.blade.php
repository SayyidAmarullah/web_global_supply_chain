@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<style>
.glass-panel {
    background: rgba(10, 17, 40, 0.4);
    backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.text-glow {
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
}

.kpi-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
.kpi-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.2); }

.nav-tabs .nav-link {
    color: rgba(255,255,255,0.6);
    border: none;
    border-bottom: 2px solid transparent;
    padding: 1rem 1.5rem;
    font-weight: 600;
}
.nav-tabs .nav-link:hover {
    color: #fff;
    border-color: transparent;
}
.nav-tabs .nav-link.active {
    color: #0dcaf0;
    background: transparent;
    border-color: transparent;
    border-bottom: 2px solid #0dcaf0;
}

/* Light Mode Overrides */
:root[data-theme="light"] .glass-panel {
    border: 1px solid rgba(0,0,0,0.05) !important;
    background: rgba(255, 255, 255, 0.8) !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}
:root[data-theme="light"] .text-white { color: #111827 !important; }
:root[data-theme="light"] .text-muted { color: #6b7280 !important; }
:root[data-theme="light"] .text-glow { text-shadow: none; }
:root[data-theme="light"] .nav-tabs .nav-link { color: #6b7280; }
:root[data-theme="light"] .nav-tabs .nav-link:hover { color: #111827; }
:root[data-theme="light"] .nav-tabs .nav-link.active { color: #0dcaf0; }
</style>

<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <span class="material-symbols-outlined fs-2 text-primary">public</span>
                Global Country Dashboard
            </h2>
            <p class="text-muted mb-0 fs-7">Unified Intelligence: Macroeconomics & Environmental Data</p>
        </div>
        <div id="headerActions" style="display: none;">
            <button class="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-2" onclick="showCountryGrid()">
                <span class="material-symbols-outlined fs-6">arrow_back</span> Back to Countries
            </button>
        </div>
    </header>

    <div class="row g-4 flex-grow-1">
        <!-- Left Panel: Country Selector -->
        <div id="countryGridContainer" class="col-lg-3 d-flex flex-column gap-4">
            <div class="glass-panel p-4 rounded-4 border-start border-4 border-primary" style="height: 800px; overflow: hidden; display: flex; flex-direction: column;">
                <h6 class="text-white fw-bold mb-3">Select Country</h6>
                
                <div class="input-group mb-4">
                    <span class="input-group-text bg-dark border-secondary text-muted">
                        <span class="material-symbols-outlined fs-5">search</span>
                    </span>
                    <input type="text" id="countrySearch" class="form-control bg-dark text-white border-secondary" placeholder="Search country..." onkeyup="filterCountries()">
                </div>

                <div id="countryList" class="d-flex flex-column gap-2 overflow-auto pe-1" style="flex-grow: 1;">
                    <!-- Injected by JS -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Unified Dashboard -->
        <div class="col-lg-9 d-flex flex-column gap-4">
            
            <!-- State 1: Select Prompt -->
            <div id="selectPrompt" class="glass-panel p-5 rounded-4 d-flex flex-column align-items-center justify-content-center h-100 border border-secondary border-opacity-25 text-center">
                <span class="material-symbols-outlined text-muted" style="font-size: 80px; opacity: 0.5;">travel_explore</span>
                <h4 class="text-white fw-bold mt-4">Select a Country</h4>
                <p class="text-muted">Choose a country from the left panel to view its unified intelligence dashboard.</p>
            </div>

            <!-- State 2: Loading -->
            <div id="loadingOverlay" class="glass-panel p-5 rounded-4 flex-column align-items-center justify-content-center h-100 border border-secondary border-opacity-25 text-center" style="display: none !important;">
                <div class="spinner-border text-primary mb-4" style="width: 3rem; height: 3rem;" role="status"></div>
                <h5 class="text-white fw-bold">Compiling Unified Data...</h5>
                <p class="text-muted">Fetching macroeconomics and satellite weather telemetry.</p>
            </div>

            <!-- State 3: Dashboard -->
            <div id="dashboardContent" style="display: none !important; flex-direction: column; gap: 1.5rem;" class="h-100">
                <!-- Country Header -->
                <div class="glass-panel p-4 rounded-4 border border-secondary border-opacity-25 position-relative overflow-hidden">
                    <div class="d-flex align-items-center position-relative z-1 w-100">
                        <span id="targetFlag" class="fi fi-eu rounded shadow-lg border border-secondary border-opacity-25 me-4" style="width: 100px; height: 75px; background-size: cover; background-position: center; display: inline-block; flex-shrink: 0;"></span>
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <h1 id="countryNameDisplay" class="text-white fw-bold mb-1 text-glow">Country Name</h1>
                                <p id="countryRegionDisplay" class="text-muted fs-6 mb-0">Region / Subregion</p>
                            </div>
                            <button id="favoriteBtn" class="btn btn-outline-warning rounded-pill d-flex align-items-center gap-2 px-4 shadow-none" onclick="toggleFavorite()">
                                <span class="material-symbols-outlined fs-6" id="favoriteIcon">star_border</span> 
                                <span id="favoriteText">Monitor Country</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs border-secondary border-opacity-25" id="dashboardTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active d-flex align-items-center gap-2" id="macro-tab" data-bs-toggle="tab" data-bs-target="#macro-pane" type="button" role="tab" aria-selected="true">
                            <span class="material-symbols-outlined fs-5">account_balance</span> Macroeconomics
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="env-tab" data-bs-toggle="tab" data-bs-target="#env-pane" type="button" role="tab" aria-selected="false">
                            <span class="material-symbols-outlined fs-5">cloud</span> Environment & Profile
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content flex-grow-1" id="dashboardTabsContent">
                    
                    <!-- TAB 1: MACROECONOMICS -->
                    <div class="tab-pane fade show active h-100" id="macro-pane" role="tabpanel" tabindex="0">
                        <div class="row g-4 mb-3">
                            <div class="col-12 text-end mb-2">
                                <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fs-7 d-flex align-items-center gap-1 d-inline-flex">
                                    <span class="material-symbols-outlined fs-6">verified</span> World Bank Verified
                                </span>
                            </div>
                            
                            <!-- GDP -->
                            <div class="col-md-6 col-lg-4">
                                <div class="glass-panel p-4 rounded-4 h-100 kpi-card border-top border-4 border-success">
                                    <div class="d-flex justify-content-between align-items-start mb-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="p-2 bg-success bg-opacity-10 rounded-3">
                                                <span class="material-symbols-outlined text-success fs-4">account_balance_wallet</span>
                                            </div>
                                            <h6 class="text-muted text-uppercase fw-bold fs-8 mb-0 tracking-wide">Gross Domestic Product</h6>
                                        </div>
                                    </div>
                                    <h2 id="gdpVal" class="text-white fw-bold mb-1">...</h2>
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
                                    <h2 id="inflationVal" class="text-white fw-bold mb-1">...</h2>
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
                                    <h2 id="populationVal" class="text-white fw-bold mb-1">...</h2>
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
                                    <h2 id="exportVal" class="text-white fw-bold mb-1">...</h2>
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
                                    <h2 id="importVal" class="text-white fw-bold mb-1">...</h2>
                                    <p class="text-muted fs-8 mb-0">Goods and Services (Current US$)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: ENVIRONMENT & PROFILE -->
                    <div class="tab-pane fade h-100" id="env-pane" role="tabpanel" tabindex="0">
                        <div class="row g-4">
                            
                            <!-- Profile Data -->
                            <div class="col-md-6">
                                <div class="glass-panel p-4 rounded-4 h-100 border border-secondary border-opacity-25">
                                    <h5 class="text-white fw-bold mb-4 d-flex align-items-center gap-2">
                                        <span class="material-symbols-outlined text-primary">badge</span>
                                        Country Profile
                                    </h5>
                                    
                                    <div class="row g-3 mb-4">
                                        <div class="col-6">
                                            <p class="text-muted fs-8 text-uppercase fw-bold mb-1">Official Currency</p>
                                            <h4 id="currencyVal" class="text-white fw-bold mb-0">...</h4>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-muted fs-8 text-uppercase fw-bold mb-1">Spoken Languages</p>
                                            <h5 id="languageVal" class="text-white fw-bold mb-0" style="word-wrap: break-word;">...</h5>
                                        </div>
                                    </div>
                                    
                                    <hr class="border-secondary border-opacity-25 mb-4">
                                    
                                    <p class="text-muted fs-8 text-uppercase fw-bold mb-2"><span class="material-symbols-outlined fs-6 align-middle me-1">map</span> Peta Negara</p>
                                    <div class="rounded-3 overflow-hidden border border-secondary border-opacity-25" style="height: 220px;">
                                        <div id="countryMap" style="width: 100%; height: 100%;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Weather Data -->
                            <div class="col-md-6">
                                <div class="glass-panel p-4 rounded-4 h-100 border-start border-4 border-info">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="text-white fw-bold mb-0 d-flex align-items-center gap-2">
                                            <span class="material-symbols-outlined text-info">satellite_alt</span>
                                            Live Satellite Weather
                                        </h5>
                                        <span class="badge bg-success bg-opacity-25 text-success">Open-Meteo</span>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-6">
                                            <p class="text-muted fs-8 text-uppercase fw-bold mb-1">Temperature</p>
                                            <h3 id="tempVal" class="text-white fw-bold mb-0">...</h3>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-muted fs-8 text-uppercase fw-bold mb-1">Condition</p>
                                            <h3 id="conditionVal" class="text-info fw-bold mb-0">...</h3>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-muted fs-8 text-uppercase fw-bold mb-1">Wind Speed</p>
                                            <h3 id="windVal" class="text-white fw-bold mb-0">...</h3>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-muted fs-8 text-uppercase fw-bold mb-1">Precipitation</p>
                                            <h3 id="precipVal" class="text-white fw-bold mb-0">...</h3>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 p-3 rounded-3" id="stormAlertBox" style="background: rgba(25, 135, 84, 0.1); border: 1px solid #198754;">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="material-symbols-outlined fs-2 text-success" id="stormAlertIcon">check_circle</span>
                                            <div>
                                                <h6 class="text-success fw-bold mb-1" id="stormAlertTitle">Safe Conditions</h6>
                                                <p class="text-muted fs-8 mb-0" id="stormAlertDesc">No severe storms detected.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            
        </div>
    </div>
</main>

<script>
let allCountriesData = [];
let mapInstance = null;
let currentMapMarker = null;
let favorites = @json($favorites ?? []);
let currentCountryCode = null;

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('https://cdn.jsdelivr.net/gh/mledoze/countries@master/countries.json');
        allCountriesData = await response.json();
        
        // Sort alphabetically
        allCountriesData.sort((a, b) => a.name.common.localeCompare(b.name.common));
        
        renderCountryList();
    } catch (error) {
        console.error("Failed to load countries list", error);
        document.getElementById('countryList').innerHTML = '<div class="text-danger text-center">Failed to load countries database.</div>';
    }

    // Fix Leaflet map sizing issue when switching to the Environment tab
    const envTabEl = document.getElementById('env-tab');
    if (envTabEl) {
        envTabEl.addEventListener('shown.bs.tab', function (event) {
            if (mapInstance) {
                setTimeout(() => {
                    mapInstance.invalidateSize();
                }, 100);
            }
        });
    }
});

function showCountryGrid() {
    document.getElementById('dashboardContent').style.setProperty('display', 'none', 'important');
    document.getElementById('headerActions').style.display = 'none';
    document.getElementById('countryGridContainer').style.display = 'flex';
    document.getElementById('selectPrompt').style.setProperty('display', 'flex', 'important');
}

function renderCountryList() {
    const listEl = document.getElementById('countryList');
    listEl.innerHTML = '';
    
    // Sort allCountriesData: favorites first, then alphabetical
    allCountriesData.sort((a, b) => {
        const aCode = a.cca2 || '';
        const bCode = b.cca2 || '';
        const aFav = favorites.includes(aCode);
        const bFav = favorites.includes(bCode);
        
        if (aFav && !bFav) return -1;
        if (!aFav && bFav) return 1;
        return a.name.common.localeCompare(b.name.common);
    });

    allCountriesData.forEach((country, index) => {
        const code = country.cca2 ? country.cca2.toLowerCase() : '';
        const name = country.name.common;
        const region = country.region;
        const isFav = favorites.includes(country.cca2);
        const favIcon = isFav ? '<span class="material-symbols-outlined text-warning fs-5">star</span>' : '';
        
        const html = `
            <div class="glass-panel p-3 rounded-3 country-item border border-secondary border-opacity-25" style="cursor: pointer; transition: all 0.2s;" onclick="selectCountry(${index})" data-name="${name.toLowerCase()}" data-region="${region.toLowerCase()}" id="country_item_${index}">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fi fi-${code} rounded" style="width: 28px; height: 20px; object-fit: cover;"></span>
                        <div class="text-truncate">
                            <h6 class="text-white fw-bold mb-0 text-truncate" style="max-width: 130px;">${name}</h6>
                            <span class="text-muted fs-8">${region}</span>
                        </div>
                    </div>
                    ${favIcon}
                </div>
            </div>
        `;
        listEl.innerHTML += html;
    });
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

async function selectCountry(index) {
    const country = allCountriesData[index];
    const code = country.cca2;
    const codeLower = code.toLowerCase();
    const name = country.name.common;
    currentCountryCode = code;
    
    updateFavoriteBtn();
    
    // Highlight selection
    document.querySelectorAll('.country-item').forEach(el => {
        el.classList.remove('bg-primary', 'bg-opacity-25', 'border-primary');
    });
    const activeEl = document.getElementById(`country_item_${index}`);
    if(activeEl) activeEl.classList.add('bg-primary', 'bg-opacity-25', 'border-primary');
    
    // Update UI states
    document.getElementById('selectPrompt').style.setProperty('display', 'none', 'important');
    document.getElementById('dashboardContent').style.setProperty('display', 'none', 'important');
    document.getElementById('loadingOverlay').style.setProperty('display', 'flex', 'important');
    document.getElementById('headerActions').style.display = 'block';
    
    // Collapse sidebar on mobile (optional, but good for responsive)
    if (window.innerWidth < 992) {
        document.getElementById('countryGridContainer').style.display = 'none';
    }
    
    try {
        // --- Populating Shared Data ---
        document.getElementById('countryNameDisplay').textContent = name;
        document.getElementById('countryRegionDisplay').textContent = `${country.region} / ${country.subregion || 'N/A'}`;
        document.getElementById('targetFlag').className = `fi fi-${codeLower} rounded shadow-lg border border-secondary border-opacity-25`;
        
        // Tab 2: Profile Data
        let currencyStr = 'N/A';
        if(country.currencies) {
            currencyStr = Object.values(country.currencies).map(cur => `${cur.name} (${cur.symbol || ''})`).join(', ');
        }
        document.getElementById('currencyVal').textContent = currencyStr;
        
        let langStr = 'N/A';
        if(country.languages) {
            langStr = Object.values(country.languages).join(', ');
        }
        document.getElementById('languageVal').textContent = langStr;

        // Leaflet Map Logic
        let lat = 0, lng = 0;
        if (country.latlng && country.latlng.length === 2) {
            lat = country.latlng[0];
            lng = country.latlng[1];
        } else if (country.capitalInfo && country.capitalInfo.latlng) {
            lat = country.capitalInfo.latlng[0];
            lng = country.capitalInfo.latlng[1];
        }
        
        // --- Fetch Tab 1: World Bank API (Macroeconomics) ---
        await fetchMacroData(code);
        
        // --- Fetch Tab 2: Open-Meteo API (Weather) ---
        await fetchWeatherData(lat, lng);
        
        // Render Map
        renderMap(lat, lng, name);

    } catch (e) {
        console.error("Error building dashboard:", e);
    } finally {
        document.getElementById('loadingOverlay').style.setProperty('display', 'none', 'important');
        document.getElementById('dashboardContent').style.setProperty('display', 'flex', 'important');
    }
}

function renderMap(lat, lng, name) {
    if (!mapInstance) {
        mapInstance = L.map('countryMap').setView([lat, lng], 4);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(mapInstance);
    } else {
        mapInstance.setView([lat, lng], 4);
    }

    if (currentMapMarker) {
        mapInstance.removeLayer(currentMapMarker);
    }
    
    currentMapMarker = L.circleMarker([lat, lng], {
        radius: 8,
        fillColor: "#0dcaf0",
        color: "#fff",
        weight: 1,
        opacity: 1,
        fillOpacity: 0.8
    }).addTo(mapInstance).bindPopup(`<b>${name}</b>`).openPopup();
    
    // Fix leaflet sizing issue inside hidden tabs by invalidating size after a short delay
    setTimeout(() => {
        mapInstance.invalidateSize();
    }, 200);
}

function updateFavoriteBtn() {
    const btn = document.getElementById('favoriteBtn');
    const icon = document.getElementById('favoriteIcon');
    const text = document.getElementById('favoriteText');
    if(favorites.includes(currentCountryCode)) {
        btn.classList.replace('btn-outline-warning', 'btn-warning');
        btn.classList.remove('text-white');
        btn.classList.add('text-dark');
        icon.textContent = 'star';
        text.textContent = 'Monitoring Active';
    } else {
        btn.classList.replace('btn-warning', 'btn-outline-warning');
        btn.classList.remove('text-dark');
        icon.textContent = 'star_border';
        text.textContent = 'Monitor Country';
    }
}

async function toggleFavorite() {
    if(!currentCountryCode) return;
    
    // Optimistic UI Update
    const isCurrentlyFav = favorites.includes(currentCountryCode);
    if (isCurrentlyFav) {
        favorites = favorites.filter(c => c !== currentCountryCode);
    } else {
        favorites.push(currentCountryCode);
    }
    updateFavoriteBtn();
    renderCountryList();

    try {
        const res = await fetch(`{{ url('intelligence/countries') }}/${currentCountryCode}/favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        // If server fails, we should revert, but for simplicity we assume success if no exception
    } catch (e) {
        console.error("Failed to toggle favorite", e);
    }
}

// Helpers for World Bank
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

async function fetchMacroData(countryCode) {
    const endpoints = {
        gdp: 'NY.GDP.MKTP.CD',
        inflation: 'FP.CPI.TOTL.ZG',
        population: 'SP.POP.TOTL',
        export: 'NE.EXP.GNFS.CD',
        import: 'NE.IMP.GNFS.CD'
    };

    // Reset UI
    document.getElementById('gdpVal').textContent = 'Loading...';
    document.getElementById('inflationVal').textContent = 'Loading...';
    document.getElementById('populationVal').textContent = 'Loading...';
    document.getElementById('exportVal').textContent = 'Loading...';
    document.getElementById('importVal').textContent = 'Loading...';

    for (const [key, indicator] of Object.entries(endpoints)) {
        try {
            const res = await fetch(`https://api.worldbank.org/v2/country/${countryCode}/indicator/${indicator}?format=json&per_page=5`);
            const data = await res.json();
            
            let value = 'N/A';
            if (data && data[1]) {
                const validEntry = data[1].find(item => item.value !== null);
                if (validEntry) {
                    value = validEntry.value;
                }
            }
            
            if (key === 'gdp' || key === 'export' || key === 'import') {
                document.getElementById(`${key}Val`).textContent = formatCurrency(value);
            } else if (key === 'inflation') {
                document.getElementById(`inflationVal`).textContent = formatPercent(value);
                const el = document.getElementById(`inflationVal`);
                if(value !== 'N/A') el.className = parseFloat(value) > 5 ? 'text-danger fw-bold mb-1' : 'text-white fw-bold mb-1';
            }
            else if (key === 'population') document.getElementById(`populationVal`).textContent = formatNumber(value);

        } catch(e) {
            document.getElementById(`${key}Val`).textContent = 'N/A';
        }
    }
}

async function fetchWeatherData(lat, lng) {
    if(lat === 0 && lng === 0) {
        setWeatherError();
        return;
    }

    try {
        const meteoUrl = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current=temperature_2m,precipitation,wind_speed_10m,weather_code,is_day`;
        const mRes = await fetch(meteoUrl);
        const mData = await mRes.json();

        if (mData.current) {
            const temp = mData.current.temperature_2m;
            const precip = mData.current.precipitation;
            const wind = mData.current.wind_speed_10m;
            const wCode = mData.current.weather_code;
            
            document.getElementById('tempVal').textContent = `${temp}°C`;
            document.getElementById('precipVal').textContent = `${precip} mm`;
            document.getElementById('windVal').textContent = `${wind} km/h`;
            
            // Map Weather Code
            let cond = 'Clear';
            if (wCode >= 1 && wCode <= 3) cond = 'Partly Cloudy';
            else if (wCode >= 45 && wCode <= 48) cond = 'Foggy';
            else if (wCode >= 51 && wCode <= 67) cond = 'Rainy';
            else if (wCode >= 71 && wCode <= 77) cond = 'Snow';
            else if (wCode >= 80 && wCode <= 82) cond = 'Heavy Rain';
            else if (wCode >= 95) cond = 'Thunderstorm';
            document.getElementById('conditionVal').textContent = cond;

            // Storm Risk Logic
            const alertBox = document.getElementById('stormAlertBox');
            const alertTitle = document.getElementById('stormAlertTitle');
            const alertDesc = document.getElementById('stormAlertDesc');
            const alertIcon = document.getElementById('stormAlertIcon');

            if (wind > 60 || precip > 20 || wCode >= 95) {
                alertBox.style.background = 'rgba(220, 53, 69, 0.1)';
                alertBox.style.border = '1px solid #dc3545';
                alertTitle.className = 'text-danger fw-bold mb-1';
                alertTitle.textContent = 'High Storm Risk';
                alertDesc.textContent = 'Dangerous weather conditions detected.';
                alertIcon.className = 'material-symbols-outlined fs-2 text-danger';
                alertIcon.textContent = 'warning';
            } else if (wind > 40 || precip > 10 || (wCode >= 80 && wCode <= 82)) {
                alertBox.style.background = 'rgba(255, 193, 7, 0.1)';
                alertBox.style.border = '1px solid #ffc107';
                alertTitle.className = 'text-warning fw-bold mb-1';
                alertTitle.textContent = 'Moderate Risk';
                alertDesc.textContent = 'Elevated wind or rain levels.';
                alertIcon.className = 'material-symbols-outlined fs-2 text-warning';
                alertIcon.textContent = 'priority_high';
            } else {
                alertBox.style.background = 'rgba(25, 135, 84, 0.1)';
                alertBox.style.border = '1px solid #198754';
                alertTitle.className = 'text-success fw-bold mb-1';
                alertTitle.textContent = 'Safe Conditions';
                alertDesc.textContent = 'No severe storms detected.';
                alertIcon.className = 'material-symbols-outlined fs-2 text-success';
                alertIcon.textContent = 'check_circle';
            }
        } else {
            setWeatherError();
        }
    } catch(e) {
        setWeatherError();
    }
}

function setWeatherError() {
    document.getElementById('tempVal').textContent = 'N/A';
    document.getElementById('precipVal').textContent = 'N/A';
    document.getElementById('windVal').textContent = 'N/A';
    document.getElementById('conditionVal').textContent = 'N/A';
}
</script>
@endsection
