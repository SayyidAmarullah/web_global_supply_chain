@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.glass-panel {
    background: rgba(10, 17, 40, 0.4);
    backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.text-glow {
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
}

.glow-info {
    border: 2px solid #0dcaf0 !important;
    box-shadow: 0 0 20px rgba(13, 202, 240, 0.6), inset 0 0 10px rgba(13, 202, 240, 0.2);
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
</style>

<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                Advanced FX Analytics
            </h2>
            <p class="text-muted mb-0 fs-7">Real-time global currency exchange rates & multi-dimensional analytics</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted fw-bold">Base Currency:</span>
            <select id="baseCurrencySelect" class="form-select bg-dark text-white border-secondary fw-bold" style="width: 150px; cursor: pointer;" onchange="changeBaseCurrency()">
                <option value="USD">USD - US Dollar</option>
                <option value="EUR">EUR - Euro</option>
                <option value="IDR">IDR - Indonesian Rupiah</option>
                <option value="GBP">GBP - British Pound</option>
                <option value="JPY">JPY - Japanese Yen</option>
                <option value="CNY">CNY - Chinese Yuan</option>
                <option value="AUD">AUD - Australian Dollar</option>
                <option value="SGD">SGD - Singapore Dollar</option>
            </select>
        </div>
    </header>

    <div class="row g-4">
        <!-- Left Panel: Currency Selector -->
        <div class="col-lg-3 d-flex flex-column gap-4">
            <div class="glass-panel p-4 rounded-4 glow-info" style="height: 800px; overflow: hidden; display: flex; flex-direction: column;">
                <h6 class="text-white fw-bold mb-3">Compare Target Currency</h6>
                
                <div class="input-group mb-4">
                    <span class="input-group-text bg-dark border-secondary text-muted">
                        <span class="material-symbols-outlined fs-5">search</span>
                    </span>
                    <input type="text" id="currencySearch" class="form-control bg-dark text-white border-secondary" placeholder="Search code..." onkeyup="filterCurrencies()">
                </div>

                <div id="currencyList" class="d-flex flex-column gap-2 overflow-auto pe-1" style="flex-grow: 1;">
                    <!-- Injected by JS -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-info" role="status"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Multi-Charts & Data -->
        <div class="col-lg-9 d-flex flex-column gap-4">
            <!-- Current Rate Top Box -->
            <div class="glass-panel p-4 rounded-4 border border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-4">
                    <div class="d-flex align-items-center">
                        <span id="baseFlag" class="fi fi-us rounded-circle shadow-lg border border-secondary border-opacity-50" style="width: 50px; height: 50px; background-size: cover; background-position: center; display: inline-block;"></span>
                        <span class="material-symbols-outlined mx-3 text-muted fs-3">sync_alt</span>
                        <span id="targetFlag" class="fi fi-eu rounded-circle shadow-lg border border-secondary border-opacity-50" style="width: 50px; height: 50px; background-size: cover; background-position: center; display: inline-block;"></span>
                    </div>
                    <div>
                        <p class="text-muted fs-8 text-uppercase fw-bold mb-1">Live Exchange Rate</p>
                        <h2 class="text-white fw-bold mb-0 text-glow">
                            1 <span id="baseCodeDisplay" class="text-muted">USD</span> = <span id="currentRateVal" class="text-info">...</span> <span id="currentCurrencyCode">EUR</span>
                        </h2>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fs-7 d-flex align-items-center gap-1">
                        <span class="material-symbols-outlined fs-6">podcasts</span> Live Broadcast
                    </span>
                    <p id="lastUpdate" class="text-muted fs-8 mt-2 mb-0">Updated: Just now</p>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="row g-4">
                <!-- Line Chart -->
                <div class="col-md-7">
                    <div class="glass-panel p-4 rounded-4 h-100 border border-secondary border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="text-white fw-bold mb-0">30-Day Volatility Trend</h6>
                            <span class="badge bg-info bg-opacity-25 text-info">Simulation</span>
                        </div>
                        <div style="height: 280px; position: relative;">
                            <canvas id="fxLineChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Radar Chart -->
                <div class="col-md-5">
                    <div class="glass-panel p-4 rounded-4 h-100 border border-secondary border-opacity-25">
                        <h6 class="text-white fw-bold mb-4">Major Currencies Relative Power</h6>
                        <div style="height: 280px; position: relative;">
                            <canvas id="fxRadarChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart Full Width -->
                <div class="col-12">
                    <div class="glass-panel p-4 rounded-4 border border-secondary border-opacity-25">
                        <h6 class="text-white fw-bold mb-4">Top 10 Strongest Exchange Rates vs Base</h6>
                        <div style="height: 250px; position: relative;">
                            <canvas id="fxBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
let allRates = {};
let fxLineChart = null;
let fxRadarChart = null;
let fxBarChart = null;
let currentSelectedCode = 'IDR';
let currentBase = 'USD';

document.addEventListener('DOMContentLoaded', async () => {
    await fetchExchangeRates();
});

async function changeBaseCurrency() {
    currentBase = document.getElementById('baseCurrencySelect').value;
    document.getElementById('baseCodeDisplay').textContent = currentBase;
    
    // Update base flag
    const baseFlag = getFlagCode(currentBase);
    document.getElementById('baseFlag').className = `fi fi-${baseFlag} rounded-circle shadow-lg border border-secondary border-opacity-50`;
    
    document.getElementById('currencyList').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-info" role="status"></div></div>';
    
    await fetchExchangeRates();
}

async function fetchExchangeRates() {
    try {
        const res = await fetch(`https://open.er-api.com/v6/latest/${currentBase}`);
        const data = await res.json();
        
        if (data && data.rates) {
            allRates = data.rates;
            
            // Format Last Update Time
            const nextUpdate = new Date(data.time_next_update_unix * 1000);
            document.getElementById('lastUpdate').textContent = `Next update: ${nextUpdate.toLocaleString()}`;
            
            renderCurrencyList();
            
            // Generate the Bar Chart for Top 10 strongest (lowest rate number means stronger currency vs base)
            generateBarChart();
            
            // Select default
            if(allRates['IDR'] && currentBase !== 'IDR') currentSelectedCode = 'IDR';
            else if(currentBase === 'IDR') currentSelectedCode = 'USD';
            else currentSelectedCode = 'EUR';
            
            selectCurrency(currentSelectedCode);
        }
    } catch (e) {
        console.error("Failed to fetch exchange rates", e);
        document.getElementById('currencyList').innerHTML = '<div class="text-danger text-center">Failed to load API</div>';
    }
}

function getFlagCode(currencyCode) {
    if (currencyCode === 'EUR') return 'eu';
    if (currencyCode === 'GBP') return 'gb';
    if (currencyCode === 'USD') return 'us';
    if (currencyCode === 'AUD') return 'au';
    if (currencyCode === 'CAD') return 'ca';
    if (currencyCode === 'CHF') return 'ch';
    if (currencyCode === 'JPY') return 'jp';
    if (currencyCode === 'CNY') return 'cn';
    if (currencyCode === 'SGD') return 'sg';
    return currencyCode.substring(0, 2).toLowerCase();
}

function renderCurrencyList() {
    const listEl = document.getElementById('currencyList');
    listEl.innerHTML = '';
    
    const codes = Object.keys(allRates).sort();
    
    codes.forEach(code => {
        if(code === currentBase) return; 
        
        const rate = allRates[code];
        const flag = getFlagCode(code);
        
        const html = `
            <div class="glass-panel p-3 rounded-3 currency-item border border-secondary border-opacity-25" style="cursor: pointer; transition: all 0.2s;" onclick="selectCurrency('${code}')" data-code="${code}" id="cur_item_${code}">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fi fi-${flag} rounded" style="width: 24px; height: 18px; object-fit: cover;"></span>
                        <h6 class="text-white fw-bold mb-0">${code}</h6>
                    </div>
                    <span class="text-muted fs-7">${rate.toFixed(2)}</span>
                </div>
            </div>
        `;
        listEl.innerHTML += html;
    });
}

function filterCurrencies() {
    const query = document.getElementById('currencySearch').value.toLowerCase();
    const items = document.querySelectorAll('.currency-item');
    
    items.forEach(item => {
        const code = item.getAttribute('data-code').toLowerCase();
        if (code.includes(query)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function selectCurrency(code) {
    currentSelectedCode = code;
    
    document.querySelectorAll('.currency-item').forEach(el => {
        el.classList.remove('bg-info', 'bg-opacity-25', 'border-info');
    });
    
    const activeEl = document.getElementById(`cur_item_${code}`);
    if(activeEl) {
        activeEl.classList.add('bg-info', 'bg-opacity-25', 'border-info');
        activeEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    const rate = allRates[code] || 1;
    document.getElementById('currentRateVal').textContent = rate.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 4});
    document.getElementById('currentCurrencyCode').textContent = code;
    
    const flag = getFlagCode(code);
    document.getElementById('targetFlag').className = `fi fi-${flag} rounded-circle shadow-lg border border-secondary border-opacity-50`;
    
    generateLineChart(code, rate);
    generateRadarChart();
}

// 1. Line Chart (Volatility)
function generateLineChart(code, currentRate) {
    const ctx = document.getElementById('fxLineChart').getContext('2d');
    if (fxLineChart) fxLineChart.destroy();
    
    const dates = [];
    const values = [];
    let simulatedRate = currentRate;
    
    for (let i = 30; i >= 0; i--) {
        const d = new Date();
        d.setDate(d.getDate() - i);
        dates.push(d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
        
        if (i === 0) {
            values.push(currentRate);
        } else {
            const change = (Math.random() * 1.6 - 0.8) / 100;
            simulatedRate = simulatedRate * (1 - change);
            values.push(simulatedRate);
        }
    }
    
    fxLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: `1 ${currentBase} = X ${code}`,
                data: values,
                borderColor: '#0dcaf0',
                backgroundColor: 'rgba(13, 202, 240, 0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#0a1128',
                pointBorderColor: '#0dcaf0',
                pointRadius: 2,
                pointHoverRadius: 5,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,0.5)', maxTicksLimit: 7 } },
                y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: 'rgba(255,255,255,0.5)' } }
            },
            interaction: { mode: 'index', intersect: false }
        }
    });
}

// 2. Radar Chart (Major Currencies)
function generateRadarChart() {
    const ctx = document.getElementById('fxRadarChart').getContext('2d');
    if (fxRadarChart) fxRadarChart.destroy();
    
    const majorCodes = ['USD', 'EUR', 'GBP', 'JPY', 'AUD', 'CAD'].filter(c => c !== currentBase);
    // Include the current selected code if not in major
    if(!majorCodes.includes(currentSelectedCode) && currentSelectedCode !== currentBase) {
        majorCodes.pop(); // remove last
        majorCodes.push(currentSelectedCode);
    }

    const dataValues = majorCodes.map(c => {
        // Normalize the rate using a log scale for better radar visual if disparities are huge (like IDR vs GBP)
        return Math.log10(allRates[c] || 1) + 5; // +5 to keep positive for drawing
    });
    
    fxRadarChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: majorCodes,
            datasets: [{
                label: `Relative Log Power vs ${currentBase}`,
                data: dataValues,
                backgroundColor: 'rgba(25, 135, 84, 0.2)',
                borderColor: '#198754',
                pointBackgroundColor: '#198754',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#198754'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                r: {
                    angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                    grid: { color: 'rgba(255, 255, 255, 0.1)' },
                    pointLabels: { color: 'rgba(255, 255, 255, 0.8)', font: { size: 12, weight: 'bold' } },
                    ticks: { display: false }
                }
            }
        }
    });
}

// 3. Bar Chart (Top 10)
function generateBarChart() {
    const ctx = document.getElementById('fxBarChart').getContext('2d');
    if (fxBarChart) fxBarChart.destroy();
    
    // Sort all rates ascending (lowest number means strongest against base)
    const sortedCurrencies = Object.entries(allRates)
        .filter(([code, rate]) => code !== currentBase)
        .sort((a, b) => a[1] - b[1])
        .slice(0, 15); // Top 15
        
    const labels = sortedCurrencies.map(item => item[0]);
    const values = sortedCurrencies.map(item => item[1]);
    
    fxBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: `Exchange Rate (1 ${currentBase} = X)`,
                data: values,
                backgroundColor: 'rgba(255, 193, 7, 0.7)',
                borderColor: '#ffc107',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,0.7)', font: { weight: 'bold' } } },
                y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: 'rgba(255,255,255,0.5)' } }
            }
        }
    });
}
</script>
@endsection
