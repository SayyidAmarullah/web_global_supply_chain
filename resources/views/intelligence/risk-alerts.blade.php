@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h3 class="text-white fw-bold tracking-tight mb-1">
                <span class="material-symbols-outlined text-warning align-middle fs-2 me-2">security</span>
                Risk Alerts & Early Warning System
            </h3>
            <span class="text-muted fs-7">Real-time threat monitoring and predictive AI mitigation</span>
        </div>
        <div class="d-flex gap-2">
            <x-button variant="outline" icon="sync">Refresh Data</x-button>
            <x-button variant="danger" icon="download">Export Risk Report</x-button>
        </div>
    </div>

    <!-- Row 1: Key Metrics -->
    <div class="row g-4 mb-2">
        <div class="col-md-4">
            <div class="glass-panel p-4 rounded-4 h-100 border-start border-4 border-warning transition-all hover-glow">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="text-muted fs-8 text-uppercase fw-bold">Overall Risk Score</span>
                    <span class="material-symbols-outlined text-warning fs-3">query_stats</span>
                </div>
                <h1 class="text-white fw-bold mb-0 display-4 text-glow">{{ $globalRisk['overall'] }}</h1>
                <p class="text-warning fw-medium mt-2 mb-0 d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined fs-6">trending_up</span> 
                    Risk Status: {{ $globalRisk['level'] }}
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-panel p-4 rounded-4 h-100 border-start border-4 border-danger transition-all hover-glow">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="text-muted fs-8 text-uppercase fw-bold">Critical Threats</span>
                    <span class="material-symbols-outlined text-danger fs-3">warning</span>
                </div>
                <h1 class="text-white fw-bold mb-0 display-4 text-glow text-danger">{{ collect($activeThreats)->whereIn('severity', ['Critical', 'High'])->count() }}</h1>
                <p class="text-danger fw-medium mt-2 mb-0 d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined fs-6">emergency</span> 
                    Require immediate mitigation
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-panel p-4 rounded-4 h-100 border-start border-4 border-primary transition-all hover-glow">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="text-muted fs-8 text-uppercase fw-bold">AI Recommendations</span>
                    <span class="material-symbols-outlined text-primary fs-3">psychology</span>
                </div>
                <h1 class="text-white fw-bold mb-0 display-4 text-glow">{{ $totalAlerts }}</h1>
                <p class="text-primary fw-medium mt-2 mb-0 d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined fs-6">auto_awesome</span> 
                    Proactive mitigation strategies active
                </p>
            </div>
        </div>
    </div>

    <!-- Row 2: Radar Chart & Active Threats -->
    <div class="row g-4 flex-grow-1">
        
        <!-- Risk Breakdown Chart -->
        <div class="col-lg-5">
            <div class="glass-panel p-4 rounded-4 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-white fw-bold mb-0 d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-info">radar</span> Global Risk Breakdown
                    </h5>
                </div>
                
                <div class="flex-grow-1 d-flex justify-content-center align-items-center position-relative w-100 h-100" style="min-height: 300px;">
                    <canvas id="riskRadarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Active Threat Feed -->
        <div class="col-lg-7">
            <div class="glass-panel p-4 rounded-4 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-white fw-bold mb-0 d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-danger">satellite_alt</span> Live Threat Feed
                    </h5>
                    <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25" style="animation: pulse 2s infinite;">Live</span>
                </div>
                
                <div id="threatFeed" class="d-flex flex-column gap-3 overflow-auto flex-grow-1 pe-2" style="max-height: 400px;">
                    @foreach($activeThreats as $threat)
                        <div class="p-3 bg-white bg-opacity-5 rounded-3 border border-secondary border-opacity-25 transition-all hover-glow cursor-pointer">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    @if($threat['severity'] === 'Critical')
                                        <span class="material-symbols-outlined text-danger fs-5">gpp_bad</span>
                                        <span class="badge bg-danger">CRITICAL</span>
                                    @elseif($threat['severity'] === 'High')
                                        <span class="material-symbols-outlined text-warning fs-5">warning</span>
                                        <span class="badge bg-warning text-dark">HIGH</span>
                                    @else
                                        <span class="material-symbols-outlined text-info fs-5">info</span>
                                        <span class="badge bg-info text-dark">MEDIUM</span>
                                    @endif
                                    <span class="text-muted fs-8">{{ $threat['id'] }}</span>
                                </div>
                                <span class="text-muted fs-8">{{ $threat['time'] }}</span>
                            </div>
                            <h5 class="text-white fw-bold mb-1">{{ $threat['title'] }}</h5>
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <span class="text-muted fs-8 d-flex align-items-center gap-1">
                                    <span class="material-symbols-outlined fs-6">category</span> {{ $threat['category'] }}
                                </span>
                                <span class="text-muted fs-8 d-flex align-items-center gap-1">
                                    <span class="material-symbols-outlined fs-6">my_location</span> {{ $threat['affected'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
    
    <!-- Row 3: AI Recommendations -->
    <div class="glass-panel p-4 rounded-4 mt-2">
        <h5 class="text-white fw-bold mb-4 d-flex align-items-center gap-2">
            <span class="material-symbols-outlined text-primary">psychology</span> Suggested Mitigations
        </h5>
        
        <div class="row g-4">
            @foreach(array_slice($recommendations, 0, 3) as $rec)
                <div class="col-md-4">
                    <div class="p-3 bg-white bg-opacity-5 rounded-3 border border-secondary border-opacity-25 h-100 transition-all hover-glow">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25">{{ $rec['type'] }}</span>
                            @if($rec['impact'] == 'High')
                                <span class="material-symbols-outlined text-danger">trending_up</span>
                            @else
                                <span class="material-symbols-outlined text-info">trending_flat</span>
                            @endif
                        </div>
                        <h6 class="text-white fw-bold mt-3 mb-2">{{ $rec['title'] }}</h6>
                        <p class="text-muted fs-7 mb-0">{{ $rec['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global Risk Data
    const riskData = @json($globalRisk['breakdown']);
    
    const ctx = document.getElementById('riskRadarChart').getContext('2d');
    
    // Create gradient
    let gradient = ctx.createRadialGradient(0, 0, 0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(239, 35, 60, 0.5)'); // Neon Red
    gradient.addColorStop(1, 'rgba(239, 35, 60, 0.0)');
    
    function getTextColor() {
        return document.documentElement.getAttribute('data-theme') === 'light' ? '#495057' : 'rgba(255, 255, 255, 0.7)';
    }
    function getGridColor() {
        return document.documentElement.getAttribute('data-theme') === 'light' ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.1)';
    }
    
    let radarChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Weather', 'Currency', 'Political', 'Port/Logistics', 'Economic'],
            datasets: [{
                label: 'Risk Vulnerability',
                data: [
                    riskData.weather, 
                    riskData.currency, 
                    riskData.political, 
                    riskData.port, 
                    riskData.economic
                ],
                backgroundColor: 'rgba(239, 35, 60, 0.2)',
                borderColor: '#ef233c', // danger color
                pointBackgroundColor: '#ef233c',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#ef233c',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: {
                        color: getGridColor()
                    },
                    grid: {
                        color: getGridColor(),
                        circular: true
                    },
                    pointLabels: {
                        color: getTextColor(),
                        font: {
                            size: 13,
                            family: "'Inter', sans-serif",
                            weight: '600'
                        }
                    },
                    ticks: {
                        display: false,
                        min: 0,
                        max: 100
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(10, 17, 40, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                }
            }
        }
    });

    window.addEventListener('theme-changed', function() {
        radarChart.options.scales.r.pointLabels.color = getTextColor();
        radarChart.options.scales.r.grid.color = getGridColor();
        radarChart.options.scales.r.angleLines.color = getGridColor();
        radarChart.update();
    });
    
    // Simulate Live WebSocket Updates
    const threatFeed = document.getElementById('threatFeed');
    const incomingThreats = [
        { id: 'TRT-106', title: 'Sudden Port Strike in Hamburg', severity: 'High', category: 'Social', affected: 'European Trade Routes', color: 'warning', icon: 'warning' },
        { id: 'TRT-107', title: 'Severe Fog at Golden Gate', severity: 'Medium', category: 'Weather', affected: 'Port of Oakland', color: 'info', icon: 'info' },
        { id: 'TRT-108', title: 'Ransomware Attack on Maersk', severity: 'Critical', category: 'Cyber/IT', affected: 'Global Logistics', color: 'danger', icon: 'gpp_bad' }
    ];
    
    let threatIndex = 0;
    
    setInterval(() => {
        if(threatIndex >= incomingThreats.length) return;
        
        const threat = incomingThreats[threatIndex];
        const newHtml = `
            <div class="p-3 bg-white bg-opacity-5 rounded-3 border border-${threat.color} border-opacity-50 transition-all hover-glow cursor-pointer" style="animation: slideIn 0.5s ease-out;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-${threat.color} fs-5">${threat.icon}</span>
                        <span class="badge bg-${threat.color} text-${threat.color === 'warning' || threat.color === 'info' ? 'dark' : 'white'}">${threat.severity.toUpperCase()}</span>
                        <span class="text-muted fs-8">${threat.id}</span>
                        <span class="badge bg-success" style="font-size: 0.6rem; animation: pulse 1.5s infinite;">NEW</span>
                    </div>
                    <span class="text-muted fs-8">Just now</span>
                </div>
                <h5 class="text-white fw-bold mb-1">${threat.title}</h5>
                <div class="d-flex align-items-center gap-3 mt-2">
                    <span class="text-muted fs-8 d-flex align-items-center gap-1">
                        <span class="material-symbols-outlined fs-6">category</span> ${threat.category}
                    </span>
                    <span class="text-muted fs-8 d-flex align-items-center gap-1">
                        <span class="material-symbols-outlined fs-6">my_location</span> ${threat.affected}
                    </span>
                </div>
            </div>
        `;
        
        threatFeed.insertAdjacentHTML('afterbegin', newHtml);
        threatIndex++;
    }, 12000); // Inject a new threat every 12 seconds
});
</script>

<style>
@keyframes slideIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.4; }
    100% { opacity: 1; }
}
</style>
@endsection
