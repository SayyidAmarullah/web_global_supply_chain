@extends('layouts.app')

@section('content')
<main class="content-area position-relative w-100 h-100 p-0 m-0 overflow-hidden" style="pointer-events: auto !important;">
    <!-- Map Container -->
    <div id="world-map" class="w-100 h-100 position-absolute top-0 start-0 z-0"></div>

    <!-- Floating Glass Header & Search -->
    <div class="position-absolute top-0 start-0 w-100 p-3 pe-none d-flex justify-content-between align-items-start z-1">
        <div class="glass-panel p-3 pe-auto rounded-4 d-flex flex-column gap-1" style="min-width: 300px;">
            <div class="d-flex align-items-center gap-2">
                <span class="material-symbols-outlined text-cyan-glow fs-4" style="color: var(--cyan-glow);">explore</span>
                <h5 class="text-white fw-bold mb-0 tracking-tight">Global Trade Map</h5>
            </div>
            <span class="text-muted fs-8 d-flex align-items-center gap-2">
                <span class="spinner-grow spinner-grow-sm text-success" role="status" style="width: 10px; height: 10px;"></span>
                Live Intelligence Stream
            </span>
        </div>

        <div class="pe-auto d-flex gap-2">
            <div class="glass-pill d-flex align-items-center px-3" style="width: 350px; height: 46px;">
                <span class="material-symbols-outlined text-muted fs-5">search</span>
                <input type="text" id="map-search" class="form-control bg-transparent border-0 text-white shadow-none" placeholder="Search Vessel, Port, Country, Commodity...">
            </div>
        </div>
    </div>

    <!-- Top Dashboard Widgets (Map Dashboard) -->
    <div class="position-absolute top-0 start-50 translate-middle-x mt-3 pe-none z-1 d-none d-xl-flex gap-3">
        <div class="glass-panel px-3 py-2 rounded-pill pe-auto d-flex align-items-center gap-3 border-cyan border-opacity-25">
            <div>
                <span class="text-muted fs-8 d-block lh-1 mb-1">Active Shipments</span>
                <span class="text-white fw-bold lh-1" id="stat-shipments">...</span>
            </div>
            <div class="border-end border-secondary h-100 mx-1"></div>
            <div>
                <span class="text-muted fs-8 d-block lh-1 mb-1">High Risk Areas</span>
                <span class="text-danger fw-bold lh-1" id="stat-risk">...</span>
            </div>
            <div class="border-end border-secondary h-100 mx-1"></div>
            <div>
                <span class="text-muted fs-8 d-block lh-1 mb-1">Avg Profit Margin</span>
                <span class="text-success fw-bold lh-1" id="stat-profit">...</span>
            </div>
        </div>
    </div>

    <!-- Floating Map Controls Bottom Left -->
    <div class="position-absolute bottom-0 start-0 p-4 pe-none z-1 d-flex gap-3 align-items-end">
        <!-- Legend -->
        <div class="glass-panel p-3 pe-auto rounded-4" style="width: 220px;">
            <h6 class="text-white fw-bold mb-3 fs-8 text-uppercase tracking-wider">Map Legend</h6>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-success fs-6">sailing</span>
                <span class="text-muted fs-8">Active Shipment</span>
            </div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-purple-neon fs-6" style="color: var(--purple-neon);">alt_route</span>
                <span class="text-muted fs-8">Redirected / AI</span>
            </div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-cyan-glow fs-6" style="color: var(--cyan-glow);">anchor</span>
                <span class="text-muted fs-8">Major Port</span>
            </div>
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="material-symbols-outlined text-danger fs-6">storm</span>
                <span class="text-muted fs-8">Weather Alert</span>
            </div>
            
            <h6 class="text-white fw-bold mb-2 fs-8 text-uppercase tracking-wider border-top border-secondary pt-2">Country Risk</h6>
            <div class="d-flex gap-1 mb-1">
                <div style="flex:1; height:4px; background:#22C55E"></div>
                <div style="flex:1; height:4px; background:#F59E0B"></div>
                <div style="flex:1; height:4px; background:#F97316"></div>
                <div style="flex:1; height:4px; background:#EF4444"></div>
            </div>
            <div class="d-flex justify-content-between text-muted fs-8">
                <span>Low</span>
                <span>Critical</span>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="d-flex flex-column gap-2 pe-auto">
            <button class="btn btn-dark glass-panel rounded-circle p-2 d-flex align-items-center justify-content-center" onclick="toggleAIPanel()" style="width:45px; height:45px;" title="Toggle AI Decision Engine">
                <span class="material-symbols-outlined text-purple-neon" style="color: var(--purple-neon);">smart_toy</span>
            </button>
            <button class="btn btn-dark glass-panel rounded-circle p-2 d-flex align-items-center justify-content-center" onclick="resetMap()" style="width:45px; height:45px;" title="Reset View">
                <span class="material-symbols-outlined">my_location</span>
            </button>
            <button class="btn btn-dark glass-panel rounded-circle p-2 d-flex align-items-center justify-content-center" onclick="toggleFullscreen()" style="width:45px; height:45px;" title="Fullscreen">
                <span class="material-symbols-outlined">fullscreen</span>
            </button>
        </div>
    </div>

    <!-- AI Decision Panel Wrapper (for vertical centering without !important conflict) -->
    <div class="position-absolute top-50 start-0 translate-middle-y ms-4" style="z-index: 1050; pointer-events: none;">
        <!-- The actual sliding panel -->
        <div id="ai-decision-panel" class="glass-panel rounded-4 border-purple border-opacity-25" style="width: 320px; transform: translateX(-150%); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); pointer-events: auto;">
            <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                <h6 class="text-white fw-bold mb-0 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined fs-5" style="color: var(--purple-neon);">smart_toy</span>
                    AI Decision Engine
                </h6>
                <button class="btn btn-link text-muted p-0" onclick="closeAIPanel()" style="pointer-events: auto; cursor: pointer; position: relative; z-index: 1060;">
                    <span class="material-symbols-outlined fs-5">close</span>
                </button>
            </div>
            <div class="p-3 overflow-auto" id="ai-decision-content" style="max-height: 60vh;">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </div>


</main>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .leaflet-control-zoom { border: none !important; box-shadow: 0 0 15px rgba(0,0,0,0.5) !important; margin-right: 20px !important; margin-bottom: 20px !important; }
    .leaflet-control-zoom-in, .leaflet-control-zoom-out { background: rgba(13, 25, 48, 0.8) !important; color: white !important; border-color: rgba(255,255,255,0.1) !important; backdrop-filter: blur(10px); }
    .leaflet-control-zoom-in:hover, .leaflet-control-zoom-out:hover { background: rgba(79, 140, 255, 0.2) !important; color: var(--cyan-glow) !important; }
    
    .animated-route {
        stroke-dasharray: 10;
        animation: dash 20s linear infinite;
    }
    @keyframes dash { to { stroke-dashoffset: -1000; } }

    .country-hover { fill-opacity: 0.5 !important; stroke: #fff !important; stroke-width: 2 !important; cursor: pointer; }
</style>

<script>
    let map;
    let layers = {
        shipments: L.layerGroup(),
        routes: L.layerGroup(),
        ports: L.layerGroup(),
        countries: L.layerGroup()
    };
    let globalCountryData = {};

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Map
        map = L.map('world-map', {
            zoomControl: false,
            attributionControl: false
        }).setView([20, 0], 3);

        let baseLayer = L.tileLayer(
            document.documentElement.getAttribute('data-theme') === 'light' 
                ? 'https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png'
                : 'https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png', 
            { subdomains: 'abcd', maxZoom: 19 }
        ).addTo(map);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Listen for Theme Change
        window.addEventListener('theme-changed', function() {
            const isLight = document.documentElement.getAttribute('data-theme') === 'light';
            baseLayer.setUrl(isLight 
                ? 'https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png'
                : 'https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png'
            );
        });

        // Add Layers to Map
        layers.countries.addTo(map);
        layers.ports.addTo(map);
        layers.shipments.addTo(map);

        // Layer Control
        L.control.layers(null, {
            "Active Shipments": layers.shipments,
            "World Ports": layers.ports,
            "Country Risk Layer": layers.countries
        }, { position: 'bottomleft', collapsed: false }).addTo(map);

        // Fetch GeoJSON for Countries first, then map data
        fetch('https://raw.githubusercontent.com/johan/world.geo.json/master/countries.geo.json')
            .then(res => res.json())
            .then(geoJson => {
                window.worldGeoJson = geoJson;
                fetchMapData();
                setInterval(fetchMapData, 30000);
            });
            
        setTimeout(() => { document.getElementById('ai-decision-panel').style.transform = 'translateX(0)'; }, 2000);
    });

    function fetchMapData() {
        fetch('{{ route("map.data") }}')
            .then(response => response.json())
            .then(data => {
                globalCountryData = data.countryRisks;
                
                // Update Top Dashboard
                document.getElementById('stat-shipments').innerText = data.stats.active_shipments;
                document.getElementById('stat-risk').innerText = data.stats.high_risk_countries;
                document.getElementById('stat-profit').innerText = data.stats.avg_profit;

                renderCountries();
                renderPorts(data.ports);
                renderShipments(data.shipments);
                renderAIPanel(data.aiDecisions);
            })
            .catch(error => console.error('Map Data Error:', error));
    }

    function renderCountries() {
        layers.countries.clearLayers();
        
        L.geoJSON(window.worldGeoJson, {
            style: function(feature) {
                const name = feature.properties.name;
                const risk = globalCountryData[name] || { score: 10, color: '#22C55E' }; // Default low risk
                
                return {
                    fillColor: risk.color,
                    weight: 1,
                    opacity: 1,
                    color: '#0A1128',
                    fillOpacity: 0.15
                };
            },
            onEachFeature: function(feature, layer) {
                layer.on({
                    mouseover: function(e) {
                        const l = e.target;
                        l.setStyle({ fillOpacity: 0.5, weight: 2, color: '#fff' });
                        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) { l.bringToFront(); }
                    },
                    mouseout: function(e) {
                        layers.countries.resetStyle(e.target);
                    },
                    click: function(e) {
                        const name = feature.properties.name;
                        const risk = globalCountryData[name] || { score: 10, level: 'Low', gdp: 'N/A', inflation: 'N/A' };
                        
                        const popupContent = `
                            <div style="background: var(--bg-navy); color: white; padding: 10px; border-radius: 8px;">
                                <h6 style="color: var(--cyan-glow); margin-bottom: 8px; border-bottom: 1px solid #333; padding-bottom: 4px;">${name}</h6>
                                <div style="font-size: 12px; line-height: 1.6;">
                                    <strong>Risk Score:</strong> <span style="color: ${risk.score > 50 ? 'red' : 'lightgreen'}">${risk.score} (${risk.level})</span><br>
                                    <strong>GDP:</strong> ${risk.gdp}<br>
                                    <strong>Inflation:</strong> ${risk.inflation}<br>
                                </div>
                            </div>
                        `;
                        L.popup({ className: 'glass-popup' })
                            .setLatLng(e.latlng)
                            .setContent(popupContent)
                            .openOn(map);
                    }
                });
            }
        }).addTo(layers.countries);
    }

    let movingMarkers = [];
    let animationFrameId = null;

    function renderShipments(shipments) {
        layers.shipments.clearLayers();
        // Remove layers.routes since we draw routes dynamically now
        movingMarkers = [];
        if(animationFrameId) cancelAnimationFrame(animationFrameId);

        const shipIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background: var(--cyan-glow); color: black; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px var(--cyan-glow);">
                    <span class="material-symbols-outlined" style="font-size: 16px;">sailing</span>
                   </div>`,
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        const redirectedIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background: var(--purple-neon); color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px var(--purple-neon);">
                    <span class="material-symbols-outlined" style="font-size: 16px;">alt_route</span>
                   </div>`,
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        shipments.forEach(ship => {
            // Draw Marker
            const icon = ship.status === 'Redirected' ? redirectedIcon : shipIcon;
            
            // Get initial position based on progress
            let initialLatLng = getPointOnRoute(ship.route, ship.progress || 0);
            const marker = L.marker(initialLatLng, { icon: icon }).addTo(layers.shipments);
            
            marker.on('click', () => {
                window.location.href = '/shipments/' + ship.id;
            });
        });
    }

    function getPointOnRoute(route, progress) {
        if(!route || route.length < 2) return route && route[0] ? route[0] : [0,0];
        let totalSegments = route.length - 1;
        let scaledProgress = progress * totalSegments;
        let index = Math.floor(scaledProgress);
        if(index >= totalSegments) return route[totalSegments];
        
        let remainder = scaledProgress - index;
        let p1 = route[index];
        let p2 = route[index + 1];
        
        let lat = p1[0] + (p2[0] - p1[0]) * remainder;
        let lng = p1[1] + (p2[1] - p1[1]) * remainder;
        return [lat, lng];
    }

    function renderPorts(ports) {
        layers.ports.clearLayers();

        const portIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background: rgba(10,17,40,0.8); color: white; border: 2px solid var(--cyan-glow); border-radius: 4px; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                    <span class="material-symbols-outlined" style="font-size: 14px;">anchor</span>
                   </div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        ports.forEach(port => {
            const marker = L.marker([port.lat, port.lng], { icon: portIcon }).addTo(layers.ports);
            marker.bindTooltip(`<div style="font-family: 'Inter', sans-serif;"><b>${port.name}</b><br>Congestion: ${port.congestion}</div>`);
        });
    }

    function renderAIPanel(decisions) {
        let html = '';
        decisions.forEach(d => {
            html += `
                <div class="mb-3 p-3 rounded glass-pill border border-secondary border-opacity-25" style="background: rgba(255,255,255,0.03);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <x-badge variant="purple">${d.type}</x-badge>
                        <span class="text-danger fw-bold fs-8">${d.impact}</span>
                    </div>
                    <h6 class="text-white fw-bold mb-1 fs-7">${d.title}</h6>
                    <p class="text-muted fs-8 mb-2">${d.description}</p>
                    <button class="btn btn-outline-primary btn-sm w-100 fs-8 py-1">Execute Action</button>
                </div>
            `;
        });
        document.getElementById('ai-decision-content').innerHTML = html;
    }
    
    function closeAIPanel() { 
        document.getElementById('ai-decision-panel').style.transform = 'translateX(-150%)'; 
    }
    
    function toggleAIPanel() {
        const panel = document.getElementById('ai-decision-panel');
        if (panel.style.transform === 'translateX(0px)' || panel.style.transform === 'translateX(0)') {
            panel.style.transform = 'translateX(-150%)';
        } else {
            panel.style.transform = 'translateX(0)';
        }
    }

    function resetMap() { map.setView([20, 0], 3); }
    function toggleFullscreen() {
        if (!document.fullscreenElement) { document.documentElement.requestFullscreen(); }
        else { if (document.exitFullscreen) { document.exitFullscreen(); } }
    }
</script>
@endsection
