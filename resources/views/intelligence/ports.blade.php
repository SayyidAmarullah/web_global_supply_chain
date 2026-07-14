@extends('layouts.app')

@section('content')
<style>
@keyframes ping {
    0% { transform: scale(1); opacity: 0.8; }
    75%, 100% { transform: scale(2.5); opacity: 0; }
}

/* Custom Dark Glass Pagination Styles */
.pagination {
    --bs-pagination-bg: rgba(255, 255, 255, 0.15); /* Light gray transparent */
    --bs-pagination-border-color: rgba(255, 255, 255, 0.2);
    --bs-pagination-color: #f3f4f6;
    --bs-pagination-hover-bg: rgba(255, 255, 255, 0.25);
    --bs-pagination-hover-color: #ffffff;
    --bs-pagination-hover-border-color: rgba(255, 255, 255, 0.3);
    --bs-pagination-focus-bg: rgba(255, 255, 255, 0.25);
    --bs-pagination-focus-color: #ffffff;
    --bs-pagination-active-bg: rgba(56, 189, 248, 0.5); /* Cyan highlight */
    --bs-pagination-active-border-color: rgba(56, 189, 248, 0.8);
    --bs-pagination-active-color: #ffffff;
    --bs-pagination-disabled-bg: rgba(255, 255, 255, 0.05);
    --bs-pagination-disabled-border-color: rgba(255, 255, 255, 0.1);
    --bs-pagination-disabled-color: rgba(255, 255, 255, 0.4);
    gap: 6px;
}
.page-link {
    border-radius: 8px !important;
    backdrop-filter: blur(8px);
    transition: all 0.2s ease;
    border: 1px solid var(--bs-pagination-border-color);
}
.page-item:first-child .page-link, .page-item:last-child .page-link {
    border-radius: 8px !important;
}
.text-muted {
    color: #9ca3af !important;
}

/* Force pagination container to be transparent and text to be visible */
nav.d-flex.justify-items-center {
    background: transparent !important;
    width: 100%;
}
nav.d-flex .small.text-muted {
    color: #9ca3af !important;
}
nav.d-flex .small.text-muted .fw-semibold {
    color: #ffffff !important;
    font-weight: 600;
}

/* Light Mode Overrides for Pagination */
:root[data-theme="light"] .pagination {
    --bs-pagination-bg: rgba(0, 0, 0, 0.04);
    --bs-pagination-border-color: rgba(0, 0, 0, 0.1);
    --bs-pagination-color: #4b5563;
    --bs-pagination-hover-bg: rgba(0, 0, 0, 0.08);
    --bs-pagination-hover-color: #111827;
    --bs-pagination-hover-border-color: rgba(0, 0, 0, 0.2);
    --bs-pagination-focus-bg: rgba(0, 0, 0, 0.08);
    --bs-pagination-focus-color: #111827;
    --bs-pagination-active-bg: rgba(56, 189, 248, 1);
    --bs-pagination-active-border-color: rgba(56, 189, 248, 1);
    --bs-pagination-active-color: #ffffff;
    --bs-pagination-disabled-bg: transparent;
    --bs-pagination-disabled-border-color: rgba(0, 0, 0, 0.05);
    --bs-pagination-disabled-color: rgba(0, 0, 0, 0.3);
}
:root[data-theme="light"] nav.d-flex .small.text-muted {
    color: #6b7280 !important;
}
:root[data-theme="light"] nav.d-flex .small.text-muted .fw-semibold {
    color: #111827 !important;
}
</style>
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <span class="material-symbols-outlined fs-2 text-cyan-glow">anchor</span>
                Global Port Intelligence
            </h2>
            <p class="text-muted mb-0 fs-7">Real-time port congestion, weather conditions, and operational delays</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn glass-pill text-white border-secondary">
                <span class="material-symbols-outlined fs-6 me-1">filter_alt</span> Filter Region
            </button>
            <button class="btn glass-pill text-cyan-glow border-cyan-glow">
                <span class="material-symbols-outlined fs-6 me-1">refresh</span> Sync Live Data
            </button>
        </div>
    </header>

    <!-- KPI Summary -->
    <div class="row g-4 mb-2">
        <div class="col-md-3">
            <div class="glass-panel p-4 rounded-4 h-100 position-relative overflow-hidden d-flex flex-column justify-content-center">
                <div class="position-absolute top-50 translate-middle-y end-0 pe-2 opacity-25" style="transform: translateY(-50%) translateX(10%);">
                    <span class="material-symbols-outlined" style="font-size: 80px;">waves</span>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-muted text-uppercase fw-bold fs-8 mb-1 tracking-wide">Tracked Ports</h6>
                    <h2 class="text-white fw-bold mb-0" style="font-size: 2.5rem;">{{ $totalPorts }}</h2>
                    <div class="text-success fs-8 mt-2 d-flex align-items-center gap-1"><span class="material-symbols-outlined fs-8">sync</span> Real-time Sync</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-panel p-4 rounded-4 h-100 position-relative overflow-hidden d-flex flex-column justify-content-center">
                <div class="position-absolute top-50 translate-middle-y end-0 pe-2 opacity-25" style="transform: translateY(-50%) translateX(10%);">
                    <span class="material-symbols-outlined text-danger" style="font-size: 80px;">warning</span>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-muted text-uppercase fw-bold fs-8 mb-1 tracking-wide">High Congestion</h6>
                    <h2 class="text-danger fw-bold mb-0" style="font-size: 2.5rem;">{{ $highCongestionPercent }}%</h2>
                    <div class="text-danger fs-8 mt-2 d-flex align-items-center gap-1"><span class="material-symbols-outlined fs-8">trending_up</span> +2.1% Global Avg</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-panel p-4 rounded-4 h-100 position-relative overflow-hidden d-flex flex-column justify-content-center">
                <div class="position-absolute top-50 translate-middle-y end-0 pe-2 opacity-25" style="transform: translateY(-50%) translateX(10%);">
                    <span class="material-symbols-outlined text-warning" style="font-size: 80px;">schedule</span>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-muted text-uppercase fw-bold fs-8 mb-1 tracking-wide">Avg Wait Time</h6>
                    <h2 class="text-warning fw-bold mb-0" style="font-size: 2.5rem;">{{ $avgWaitTime }}<span class="fs-5 text-muted ms-1">Hrs</span></h2>
                    <div class="text-muted fs-8 mt-2 d-flex align-items-center gap-1"><span class="material-symbols-outlined fs-8">public</span> Across major hubs</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-panel p-4 rounded-4 h-100 position-relative overflow-hidden d-flex flex-column justify-content-center">
                <div class="position-absolute top-50 translate-middle-y end-0 pe-2 opacity-25" style="transform: translateY(-50%) translateX(10%);">
                    <span class="material-symbols-outlined text-cyan-glow" style="font-size: 80px;">gpp_good</span>
                </div>
                <div class="position-relative z-1">
                    <h6 class="text-muted text-uppercase fw-bold fs-8 mb-1 tracking-wide">Operational Status</h6>
                    <h2 class="text-cyan-glow fw-bold mb-0" style="font-size: 2.5rem;">Nominal</h2>
                    <div class="text-success fs-8 mt-2 d-flex align-items-center gap-1"><span class="material-symbols-outlined fs-8">check_circle</span> No critical closures</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 flex-grow-1">
        <!-- Ports List -->
        <div class="col-lg-8 d-flex flex-column gap-4">
            <div class="glass-panel rounded-4 p-4 flex-grow-1 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-white fw-bold mb-0">Strategic Port Status</h5>
                    <div class="input-group" style="width: 250px;">
                        <span class="input-group-text bg-transparent border-secondary text-muted border-end-0">
                            <span class="material-symbols-outlined fs-6">search</span>
                        </span>
                        <input type="text" class="form-control bg-transparent border-secondary border-start-0 text-white" placeholder="Search port...">
                    </div>
                </div>

                <div class="table-responsive flex-grow-1" style="overflow-y: auto; min-height: 0;">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead class="text-muted fs-8 text-uppercase position-sticky top-0 z-1" style="background: var(--glass-bg); backdrop-filter: blur(24px); border-bottom: 1px solid var(--glass-border);">
                            <tr>
                                <th class="py-3 font-weight-normal border-0">Port Name</th>
                                <th class="py-3 font-weight-normal border-0">Country</th>
                                <th class="py-3 font-weight-normal border-0">Congestion</th>
                                <th class="py-3 font-weight-normal border-0">Wait Time</th>
                                <th class="py-3 font-weight-normal border-0">Weather</th>
                                <th class="py-3 font-weight-normal border-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ports as $port)
                            <tr style="cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='rgba(56, 189, 248, 0.05)'" onmouseout="this.style.background='transparent'" onclick="focusPortMap({{ $port['latitude'] }}, {{ $port['longitude'] }}, '{{ $port['name'] }}', '{{ $port['congestion'] }}')">
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(56, 189, 248, 0.1);">
                                            <span class="material-symbols-outlined fs-6 text-cyan-glow">anchor</span>
                                        </div>
                                        <span class="text-white fw-medium">{{ $port['name'] }}</span>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $port['country'] }}</td>
                                <td>
                                    @if($port['congestion'] === 'High')
                                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">High</span>
                                    @elseif($port['congestion'] === 'Medium')
                                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">Medium</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">Low</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-white fw-bold">{{ $port['wait_time_hours'] }} Hrs</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 text-muted">
                                        @if($port['weather'] === 'Clear')
                                            <span class="material-symbols-outlined fs-5 text-warning">light_mode</span>
                                        @elseif($port['weather'] === 'Rain')
                                            <span class="material-symbols-outlined fs-5 text-info">rainy</span>
                                        @elseif($port['weather'] === 'Storm')
                                            <span class="material-symbols-outlined fs-5 text-danger">thunderstorm</span>
                                        @else
                                            <span class="material-symbols-outlined fs-5 text-secondary">cloud</span>
                                        @endif
                                        {{ $port['weather'] }}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm glass-pill text-cyan-glow hover-white" 
                                            onclick="event.stopPropagation();"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#portIntelModal"
                                            data-port-name="{{ $port['name'] }}"
                                            data-port-country="{{ $port['country'] }}"
                                            data-port-congestion="{{ $port['congestion'] }}"
                                            data-port-wait="{{ $port['wait_time_hours'] }}"
                                            data-port-weather="{{ $port['weather'] }}">
                                        View Intel
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3 d-flex justify-content-end" data-bs-theme="dark">
                    {{ $ports->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>

        <!-- Right Side Panel -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            
            <!-- Congestion Heatmap -->
            <div class="glass-panel p-4 rounded-4">
                <h5 class="text-white fw-bold mb-3 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-purple-neon">map</span>
                    Congestion Heatmap
                </h5>
                <div class="rounded-3 border border-secondary border-opacity-50 overflow-hidden position-relative" id="mini-port-map" style="height: 250px;">
                </div>
            </div>

            <!-- Risk AI Analysis -->
            <div class="glass-panel p-4 rounded-4 flex-grow-1 d-flex flex-column">
                <h5 class="text-white fw-bold mb-3 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-warning">model_training</span>
                    AI Port Risk Analysis
                </h5>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted fs-7">Global Port Stress Level</span>
                        <span class="text-warning fw-bold fs-7">{{ $stressLevel }} / 100</span>
                    </div>
                    <div class="progress bg-dark" style="height: 6px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $stressLevel }}%;"></div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-3 mb-4">
                    @foreach($worstPorts as $port)
                    <div class="p-3 rounded-3 border border-danger border-opacity-25" style="background: rgba(239, 68, 68, 0.05); cursor: pointer;" onclick="focusPortMap({{ $port->latitude }}, {{ $port->longitude }}, '{{ $port->name }}', '{{ $port->congestion }}')">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-danger fs-5">gpp_maybe</span>
                            <span class="text-white fw-bold fs-7">{{ $port->name }} Bottleneck</span>
                        </div>
                        <p class="text-muted fs-8 mb-0">High congestion detected. Expect delays up to {{ $port->wait_time_hours }} hours. Suggest rerouting to alternative regional ports.</p>
                    </div>
                    @endforeach

                    @foreach($bestPorts as $port)
                    <div class="p-3 rounded-3 border border-info border-opacity-25" style="background: rgba(56, 189, 248, 0.05); cursor: pointer;" onclick="focusPortMap({{ $port->latitude }}, {{ $port->longitude }}, '{{ $port->name }}', '{{ $port->congestion }}')">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-info fs-5">insights</span>
                            <span class="text-white fw-bold fs-7">{{ $port->name }} Efficiency Peak</span>
                        </div>
                        <p class="text-muted fs-8 mb-0">Turnaround times are exceptional ({{ $port->wait_time_hours }} hours max). Ideal hub for transshipment in current conditions.</p>
                    </div>
                    @endforeach
                </div>
                
                <button onclick="runDeepAnalysis(this)" class="btn btn-outline-info w-100 rounded-pill mt-auto d-flex justify-content-center align-items-center gap-2 transition-all">
                    <span class="material-symbols-outlined fs-6" id="analysisIcon">memory</span> <span id="analysisText">Run Deep Analysis</span>
                </button>
            </div>
            
        </div>
    </div>
</main>

<!-- Port Intel Modal -->
<div class="modal fade" id="portIntelModal" tabindex="-1" aria-labelledby="portIntelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-panel border-secondary rounded-4">
            <div class="modal-header border-bottom border-secondary border-opacity-25">
                <h5 class="modal-title text-white fw-bold d-flex align-items-center gap-2" id="portIntelModalLabel">
                    <span class="material-symbols-outlined text-cyan-glow">anchor</span>
                    <span id="modalPortName">Port Details</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
                    <div>
                        <h6 class="text-muted text-uppercase fs-8 fw-bold mb-1">Location</h6>
                        <h5 class="text-white mb-0" id="modalPortCountry">Country</h5>
                    </div>
                    <div class="text-end">
                        <h6 class="text-muted text-uppercase fs-8 fw-bold mb-1">Status</h6>
                        <span id="modalPortStatus" class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fs-7">Active</span>
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-25 h-100">
                            <div class="d-flex align-items-center gap-2 mb-2 text-muted">
                                <span class="material-symbols-outlined fs-5">schedule</span>
                                <span class="fs-8 text-uppercase fw-bold">Wait Time</span>
                            </div>
                            <h3 class="text-white fw-bold mb-0"><span id="modalPortWait">0</span> <span class="fs-6 text-muted">Hrs</span></h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-25 h-100">
                            <div class="d-flex align-items-center gap-2 mb-2 text-muted">
                                <span class="material-symbols-outlined fs-5">thunderstorm</span>
                                <span class="fs-8 text-uppercase fw-bold">Weather</span>
                            </div>
                            <h3 class="text-white fw-bold mb-0" id="modalPortWeather">Clear</h3>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 rounded-3" style="background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2);">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-purple-neon fs-5">psychology</span>
                        <span class="text-white fw-bold fs-7">AI Recommendation</span>
                    </div>
                    <p class="text-muted fs-8 mb-0" id="modalPortAI">AI is analyzing the current data...</p>
                </div>
            </div>
            <div class="modal-footer border-top border-secondary border-opacity-25">
                <button type="button" class="btn btn-dark text-white rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('shipments.index') }}" id="modalRerouteBtn" class="btn btn-primary rounded-pill px-4" style="background: var(--cyan-glow); border: none;">Reroute Shipment</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var portIntelModal = document.getElementById('portIntelModal')
    if (portIntelModal) {
        portIntelModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            var button = event.relatedTarget
            
            // Extract info from data-* attributes
            var name = button.getAttribute('data-port-name')
            var country = button.getAttribute('data-port-country')
            var congestion = button.getAttribute('data-port-congestion')
            var wait = button.getAttribute('data-port-wait')
            var weather = button.getAttribute('data-port-weather')
            
            // Update the modal's content
            document.getElementById('modalPortName').textContent = name
            document.getElementById('modalPortCountry').textContent = country
            document.getElementById('modalPortWait').textContent = wait
            document.getElementById('modalPortWeather').textContent = weather
            
            // Update the reroute button link
            document.getElementById('modalRerouteBtn').href = "{{ route('shipments.index') }}?port=" + encodeURIComponent(name);
            
            // Update Status Badge
            var statusBadge = document.getElementById('modalPortStatus');
            statusBadge.className = 'badge px-3 py-2 rounded-pill fs-7'; // Reset classes
            
            var aiText = "";
            if(congestion === 'High') {
                statusBadge.classList.add('bg-danger', 'bg-opacity-25', 'text-danger', 'border', 'border-danger', 'border-opacity-25');
                statusBadge.textContent = 'High Congestion';
                aiText = "Critical delays expected. We strongly advise rerouting to alternative regional ports. Processing times are degraded by 45%.";
            } else if(congestion === 'Medium') {
                statusBadge.classList.add('bg-warning', 'bg-opacity-25', 'text-warning', 'border', 'border-warning', 'border-opacity-25');
                statusBadge.textContent = 'Medium Congestion';
                aiText = "Moderate delays. Berthing times are slower than usual but manageable. Keep an eye on incoming weather systems.";
            } else {
                statusBadge.classList.add('bg-success', 'bg-opacity-25', 'text-success', 'border', 'border-success', 'border-opacity-25');
                statusBadge.textContent = 'Low Congestion';
                aiText = "Optimal conditions. Turnaround times are highly efficient. Highly recommended for transshipment routing right now.";
            }
            
            document.getElementById('modalPortAI').textContent = aiText;
        })
    }
})
</script>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
/* Button animation classes */
.spin-animation {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
<script>
var miniMap = null;
var portMarkers = {}; // Store markers to open popup

document.addEventListener('DOMContentLoaded', function() {
    var mapElement = document.getElementById('mini-port-map');
    if(mapElement) {
        miniMap = L.map('mini-port-map', {
            zoomControl: false,
            attributionControl: false
        }).setView([20, 0], 1);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png', { 
            subdomains: 'abcd', maxZoom: 19 
        }).addTo(miniMap);

        var mapPorts = @json($mapPorts);

        mapPorts.forEach(function(port) {
            if(port.latitude && port.longitude) {
                var color = '#38BDF8'; // Low (Cyan)
                if (port.congestion === 'High') color = '#EF4444'; // Red
                else if (port.congestion === 'Medium') color = '#F59E0B'; // Orange
                
                var marker = L.circleMarker([port.latitude, port.longitude], {
                    radius: port.congestion === 'High' ? 5 : 3,
                    fillColor: color,
                    color: port.congestion === 'High' ? '#ffffff' : color,
                    weight: port.congestion === 'High' ? 2 : 1,
                    opacity: 1,
                    fillOpacity: port.congestion === 'High' ? 1 : 0.6
                }).addTo(miniMap)
                .bindPopup('<div class="text-dark"><b>' + port.name + '</b><br>Congestion: ' + port.congestion + '</div>', {
                    closeButton: false,
                    className: 'glass-popup-sm'
                });
                
                // Keep reference
                portMarkers[port.name] = marker;
            }
        });
    }
});

function focusPortMap(lat, lng, name, congestion) {
    if(miniMap && lat && lng) {
        // Fly directly to the port
        miniMap.flyTo([lat, lng], 6, {
            duration: 1.5
        });
        
        // Open the tooltip/popup if marker exists
        if(portMarkers[name]) {
            setTimeout(() => {
                portMarkers[name].openPopup();
            }, 1500); // open popup when flying ends
        }
    }
}

function runDeepAnalysis(btn) {
    var icon = document.getElementById('analysisIcon');
    var text = document.getElementById('analysisText');
    
    // Set loading state
    icon.classList.add('spin-animation');
    icon.textContent = 'autorenew';
    text.textContent = 'Fetching Global API...';
    btn.disabled = true;
    
    // Call the backend to hit Open-Meteo API
    fetch("{{ route('intelligence.deep-analysis') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Success state
            icon.classList.remove('spin-animation');
            icon.textContent = 'check_circle';
            icon.classList.remove('text-info');
            icon.classList.add('text-success');
            btn.classList.replace('btn-outline-info', 'btn-outline-success');
            text.textContent = 'Analysis Complete!';
            
            // Reload page to show new data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            // Error handling
            icon.classList.remove('spin-animation');
            icon.textContent = 'error';
            icon.classList.replace('text-info', 'text-danger');
            btn.classList.replace('btn-outline-info', 'btn-outline-danger');
            text.textContent = 'API Error';
            
            setTimeout(() => {
                icon.textContent = 'memory';
                icon.classList.replace('text-danger', 'text-info');
                btn.classList.replace('btn-outline-danger', 'btn-outline-info');
                text.textContent = 'Run Deep Analysis';
                btn.disabled = false;
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        icon.classList.remove('spin-animation');
        btn.disabled = false;
    });
}
</script>
@endsection
