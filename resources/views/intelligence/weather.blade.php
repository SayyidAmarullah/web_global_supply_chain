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
                <span class="material-symbols-outlined fs-2 text-info">storm</span>
                Weather Intel & Country Demographics
            </h2>
            <p class="text-muted mb-0 fs-7">Real-time data integration via Open-Meteo & REST Countries API</p>
        </div>
        <div id="headerActions" style="display: none;">
            <button class="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-2" onclick="showCountryGrid()">
                <span class="material-symbols-outlined fs-6">arrow_back</span> Back to Global View
            </button>
        </div>
    </header>

    <!-- State 1: Global Loading Initializer -->
    <div id="initialLoading" class="d-flex justify-content-center align-items-center flex-grow-1">
        <div class="text-center">
            <div class="spinner-border text-info mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
            <h5 class="text-white">Connecting to REST Countries API...</h5>
            <p class="text-muted">Fetching 250+ Global Territories</p>
        </div>
    </div>

    <!-- State 2: Country Selection Grid -->
    <div id="countryGridContainer" class="flex-grow-1" style="display: none;">
        <div class="mb-4">
            <div class="input-group" style="max-width: 400px;">
                <span class="input-group-text bg-dark border-secondary text-muted">
                    <span class="material-symbols-outlined fs-5">search</span>
                </span>
                <input type="text" id="countrySearch" class="form-control bg-dark text-white border-secondary" placeholder="Search countries or regions..." onkeyup="filterCountries()">
            </div>
        </div>

        <div class="row g-3" id="countriesRow">
            <!-- Dynamic Injection -->
        </div>
    </div>

        <!-- State 3: Data Loading Overlay -->
    <div id="loadingOverlay" class="d-flex justify-content-center align-items-center flex-grow-1" style="display: none !important;">
        <div class="text-center">
            <div class="spinner-border text-info mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
            <h5 class="text-white">Connecting to Open-Meteo Satellites...</h5>
            <p class="text-muted">Processing meteorological algorithms</p>
        </div>
    </div>

    <!-- State 4: Data Container -->
    <div id="dataContainer" class="d-flex flex-column gap-4 flex-grow-1" style="display: none !important;">
        
        <div class="row g-4 mb-2">
            
            <!-- Rest Countries Panel -->
            <div class="col-md-5">
                <div class="glass-panel p-4 rounded-4 h-100 border-start border-4 border-primary">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span id="flagIcon" class="fi rounded shadow-sm" style="width: 60px; height: 40px; font-size: 40px; background-size: cover; background-position: center;"></span>
                        <div>
                            <p class="text-muted fs-8 text-uppercase fw-bold mb-0">Negara</p>
                            <h4 id="countryNameDisplay" class="text-white fw-bold mb-0 text-glow">Country Name</h4>
                            <p class="text-muted fs-8 text-uppercase fw-bold mb-0 mt-2">Wilayah</p>
                            <span id="countryRegionDisplay" class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25">Region</span>
                        </div>
                    </div>
                    
                    <hr class="border-secondary border-opacity-25">
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <p class="text-muted fs-8 text-uppercase fw-bold mb-1"><span class="material-symbols-outlined fs-6 align-middle me-1">payments</span> Mata uang</p>
                            <h5 id="currencyVal" class="text-white fw-medium">Loading...</h5>
                        </div>
                        
                        <div class="col-6">
                            <p class="text-muted fs-8 text-uppercase fw-bold mb-1"><span class="material-symbols-outlined fs-6 align-middle me-1">translate</span> Bahasa</p>
                            <h5 id="languageVal" class="text-white fw-medium">Loading...</h5>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-muted fs-8 text-uppercase fw-bold mb-2"><span class="material-symbols-outlined fs-6 align-middle me-1">map</span> Peta Negara</p>
                        <div class="rounded-3 overflow-hidden border border-secondary border-opacity-25" style="height: 200px;">
                            <div id="countryMap" style="width: 100%; height: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Open Meteo Panel -->
            <div class="col-md-7">
                <div class="glass-panel p-4 rounded-4 h-100 border-start border-4 border-info d-flex flex-column">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-white fw-bold mb-0 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined fs-4 text-info">satellite_alt</span> Current Weather Conditions
                        </h5>
                        <span id="weatherTime" class="text-muted fs-8">Real-time</span>
                    </div>

                    <div class="d-flex flex-column flex-grow-1 gap-3">
                        <!-- Row 1 -->
                        <div class="row g-3 flex-grow-1">
                            <div class="col-sm-4">
                                <div class="bg-white bg-opacity-5 rounded-3 border border-secondary border-opacity-25 h-100 d-flex flex-column justify-content-center align-items-center transition-all hover-glow p-3">
                                    <span class="material-symbols-outlined text-info mb-2" style="font-size: 3rem;">thermostat</span>
                                    <p class="text-muted fs-8 text-uppercase mb-1">Temperature</p>
                                    <h4 id="tempVal" class="text-white fw-bold mb-0">--°C</h4>
                                </div>
                            </div>
                            
                            <div class="col-sm-4">
                                <div class="bg-white bg-opacity-5 rounded-3 border border-secondary border-opacity-25 h-100 d-flex flex-column justify-content-center align-items-center transition-all hover-glow p-3">
                                    <span class="material-symbols-outlined text-warning mb-2" style="font-size: 3rem;">air</span>
                                    <p class="text-muted fs-8 text-uppercase mb-1">Wind Speed</p>
                                    <h4 id="windVal" class="text-white fw-bold mb-0">-- km/h</h4>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div id="riskContainer" class="bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25 h-100 d-flex flex-column justify-content-center align-items-center transition-all hover-glow p-3">
                                    <span id="riskIcon" class="material-symbols-outlined text-success mb-2" style="font-size: 3rem;">verified_user</span>
                                    <p class="text-muted fs-8 text-uppercase mb-1">Storm Risk</p>
                                    <h4 id="riskVal" class="text-success fw-bold mb-0">Low</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="row g-3 flex-grow-1">
                            <div class="col-sm-4">
                                <div class="bg-white bg-opacity-5 rounded-3 border border-secondary border-opacity-25 h-100 d-flex flex-column justify-content-center align-items-center transition-all hover-glow p-3">
                                    <span class="material-symbols-outlined text-primary mb-2" style="font-size: 3rem;">explore</span>
                                    <p class="text-muted fs-8 text-uppercase mb-1">Wind Direction</p>
                                    <h4 id="windDirVal" class="text-white fw-bold mb-0">--°</h4>
                                </div>
                            </div>
                            
                            <div class="col-sm-4">
                                <div class="bg-white bg-opacity-5 rounded-3 border border-secondary border-opacity-25 h-100 d-flex flex-column justify-content-center align-items-center transition-all hover-glow p-3">
                                    <span class="material-symbols-outlined text-secondary mb-2" style="font-size: 3rem;">partly_cloudy_day</span>
                                    <p class="text-muted fs-8 text-uppercase mb-1">Condition</p>
                                    <h4 id="conditionVal" class="text-white fw-bold mb-0 text-truncate text-center w-100 px-2" style="max-width: 100%;">--</h4>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="bg-white bg-opacity-5 rounded-3 border border-secondary border-opacity-25 h-100 d-flex flex-column justify-content-center align-items-center transition-all hover-glow p-3">
                                    <span id="dayNightIcon" class="material-symbols-outlined text-warning mb-2" style="font-size: 3rem;">light_mode</span>
                                    <p class="text-muted fs-8 text-uppercase mb-1">Time of Day</p>
                                    <h4 id="dayNightVal" class="text-white fw-bold mb-0">--</h4>
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

document.addEventListener('DOMContentLoaded', async () => {
    try {
        // Use a reliable GitHub CDN instead of restcountries.com to avoid deprecation/blocking errors.
        const response = await fetch('https://cdn.jsdelivr.net/gh/mledoze/countries@master/countries.json');
        
        if (!response.ok) throw new Error("API Network response was not ok");
        
        const data = await response.json();
        
        if (!Array.isArray(data)) throw new Error("Invalid data format received");
        
        // Sort alphabetically
        data.sort((a, b) => a.name.common.localeCompare(b.name.common));
        allCountriesData = data;
        
        renderCountries(data);
        
        document.getElementById('initialLoading').style.setProperty('display', 'none', 'important');
        document.getElementById('countryGridContainer').style.display = 'block';
    } catch (e) {
        console.error("Failed to load Countries Database:", e);
        document.getElementById('initialLoading').innerHTML = `
            <div class="text-center text-danger">
                <span class="material-symbols-outlined fs-1 mb-2">error</span>
                <h5 class="text-white">Connection Failed</h5>
                <p class="text-muted">Unable to connect to Countries Global Database. Please try again later.</p>
                <button class="btn btn-outline-danger mt-3" onclick="location.reload()">Refresh Page</button>
            </div>
        `;
    }
});

function renderCountries(countries) {
    const row = document.getElementById('countriesRow');
    row.innerHTML = '';
    
    countries.forEach((c, index) => {
        const name = c.name.common;
        const region = c.region || 'Unknown';
        const code = c.cca2 ? c.cca2.toLowerCase() : '';
        
        const html = `
        <div class="col-md-4 col-lg-3 country-item" data-name="${name.toLowerCase()}" data-region="${region.toLowerCase()}">
            <div class="glass-panel p-3 rounded-4 h-100 country-card border border-secondary border-opacity-25" onclick="selectCountry(${index})">
                <div class="d-flex align-items-center gap-3">
                    <span class="fi fi-${code} rounded shadow-sm border border-secondary border-opacity-25" style="width: 32px; height: 24px; background-size: cover; background-position: center; display: inline-block;"></span>
                    <div class="text-truncate">
                        <h6 class="text-white fw-bold mb-0 text-truncate">${name}</h6>
                        <span class="text-muted" style="font-size: 0.7rem;">${region}</span>
                    </div>
                </div>
            </div>
        </div>
        `;
        row.innerHTML += html;
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

function showCountryGrid() {
    document.getElementById('dataContainer').style.setProperty('display', 'none', 'important');
    document.getElementById('headerActions').style.display = 'none';
    document.getElementById('countryGridContainer').style.display = 'block';
}

async function selectCountry(index) {
    const country = allCountriesData[index];
    const code = country.cca2 ? country.cca2.toLowerCase() : '';
    
    document.getElementById('countryGridContainer').style.display = 'none';
    document.getElementById('loadingOverlay').style.setProperty('display', 'flex', 'important');
    
    try {
        // --- 1. Populate Country Data ---
        document.getElementById('countryNameDisplay').textContent = country.name.common;
        document.getElementById('countryRegionDisplay').textContent = `${country.region} / ${country.subregion || 'N/A'}`;
        document.getElementById('flagIcon').className = `fi fi-${code} rounded shadow-sm`;
        
        // Extract currencies
        let currencyStr = 'Unknown';
        if(country.currencies) {
            currencyStr = Object.values(country.currencies).map(cur => `${cur.name} (${cur.symbol})`).join(', ');
        }
        document.getElementById('currencyVal').textContent = currencyStr;
        
        // Extract languages
        let langStr = 'Unknown';
        if(country.languages) {
            langStr = Object.values(country.languages).join(', ');
        }
        document.getElementById('languageVal').textContent = langStr;

        // --- 2. Fetch Open-Meteo Data & Update Map ---
        const lat = country.latlng[0];
        const lng = country.latlng[1];
        
        if (window.leafletMap) {
            window.leafletMap.remove();
        }
        window.leafletMap = L.map('countryMap').setView([lat, lng], 4);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(window.leafletMap);
        L.marker([lat, lng]).addTo(window.leafletMap)
            .bindPopup(country.name.common)
            .openPopup();
        
        const res = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current_weather=true`);
        const weatherData = await res.json();
        
        if(weatherData && weatherData.current_weather) {
            const current = weatherData.current_weather;
            document.getElementById('tempVal').textContent = `${current.temperature}°C`;
            document.getElementById('windVal').textContent = `${current.windspeed} km/h`;
            document.getElementById('weatherTime').textContent = `Local Time Sync: ${current.time.replace('T', ' ')}`;
            
            // Fill New Data Boxes
            document.getElementById('windDirVal').textContent = `${current.winddirection}°`;
            
            if (current.is_day === 1) {
                document.getElementById('dayNightVal').textContent = 'Siang';
                document.getElementById('dayNightIcon').className = 'material-symbols-outlined text-warning';
                document.getElementById('dayNightIcon').textContent = 'light_mode';
            } else {
                document.getElementById('dayNightVal').textContent = 'Malam';
                document.getElementById('dayNightIcon').className = 'material-symbols-outlined text-info';
                document.getElementById('dayNightIcon').textContent = 'dark_mode';
            }
            
            let cond = 'Unknown';
            const code = current.weathercode;
            if(code === 0) cond = 'Cerah';
            else if(code <= 3) cond = 'Berawan';
            else if(code <= 48) cond = 'Berkabut';
            else if(code <= 55) cond = 'Gerimis';
            else if(code <= 65) cond = 'Hujan';
            else if(code <= 77) cond = 'Salju';
            else if(code >= 95) cond = 'Badai Petir';
            document.getElementById('conditionVal').textContent = cond;
            
            // Calculate pseudo storm risk based on windspeed
            const riskContainer = document.getElementById('riskContainer');
            const riskIcon = document.getElementById('riskIcon');
            const riskVal = document.getElementById('riskVal');
            
            if (current.windspeed > 40) {
                riskContainer.className = 'p-3 bg-danger bg-opacity-10 rounded-3 border border-danger border-opacity-25';
                riskIcon.className = 'material-symbols-outlined text-danger';
                riskIcon.textContent = 'warning';
                riskVal.className = 'text-danger fw-bold mb-0';
                riskVal.textContent = 'HIGH';
            } else if (current.windspeed > 20) {
                riskContainer.className = 'p-3 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-25';
                riskIcon.className = 'material-symbols-outlined text-warning';
                riskIcon.textContent = 'error';
                riskVal.className = 'text-warning fw-bold mb-0';
                riskVal.textContent = 'MEDIUM';
            } else {
                riskContainer.className = 'p-3 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25';
                riskIcon.className = 'material-symbols-outlined text-success';
                riskIcon.textContent = 'verified_user';
                riskVal.className = 'text-success fw-bold mb-0';
                riskVal.textContent = 'LOW';
            }
        } else {
            throw new Error("Weather data unavailable");
        }
        
    } catch (e) {
        console.error(e);
        document.getElementById('tempVal').textContent = 'N/A';
        document.getElementById('windVal').textContent = 'N/A';
        document.getElementById('riskVal').textContent = 'N/A';
    } finally {
        document.getElementById('loadingOverlay').style.setProperty('display', 'none', 'important');
        document.getElementById('dataContainer').style.setProperty('display', 'flex', 'important');
        document.getElementById('headerActions').style.display = 'block';
        
        // Leaflet bug fix: recalculate map size after container becomes visible
        if (window.leafletMap) {
            setTimeout(() => {
                window.leafletMap.invalidateSize();
            }, 100);
        }
    }
}
</script>
@endsection
