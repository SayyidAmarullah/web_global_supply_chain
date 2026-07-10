@extends('layouts.app')

@section('content')
<main class="content-area position-relative w-100 h-100 p-0 m-0 overflow-hidden" style="pointer-events: auto !important;">
    <!-- Map Container -->
    <div id="world-map" class="w-100 h-100 position-absolute top-0 start-0 z-0"></div>

    <!-- Floating Glass Header Overlay -->
    <div class="position-absolute top-0 start-0 w-100 p-4 pe-none d-flex justify-content-between align-items-start z-1">
        <div class="glass-panel p-3 pe-auto">
            <h4 class="text-white fw-bold mb-1 tracking-tight">Global Trade Map</h4>
            <span class="text-muted fs-7 d-flex align-items-center gap-2">
                <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                Live Tracking Engine Online
            </span>
        </div>

        <div class="glass-panel p-2 pe-auto d-flex gap-2">
            <div class="input-group input-group-sm" style="width: 250px;">
                <span class="input-group-text bg-transparent border-secondary text-muted border-end-0">
                    <span class="material-symbols-outlined fs-6">search</span>
                </span>
                <input type="text" class="form-control bg-transparent text-white border-secondary border-start-0" placeholder="Search vessel, port, country...">
            </div>
            <x-button variant="outline" class="btn-sm" icon="layers">Layers</x-button>
        </div>
    </div>

    <!-- Floating Controls Bottom Left (Legend) -->
    <div class="position-absolute bottom-0 start-0 p-4 pe-none z-1">
        <div class="glass-panel p-3 pe-auto" style="width: 250px;">
            <h6 class="text-white fw-bold mb-3 fs-7 text-uppercase">Map Legend</h6>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-success fs-5">sailing</span>
                <span class="text-muted fs-7">Active Shipment</span>
            </div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-purple-neon fs-5" style="color: var(--purple-neon);">alt_route</span>
                <span class="text-muted fs-7">Redirected Shipment</span>
            </div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-cyan-glow fs-5" style="color: var(--cyan-glow);">anchor</span>
                <span class="text-muted fs-7">Major Port</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="material-symbols-outlined text-info fs-5">storm</span>
                <span class="text-muted fs-7">Weather Alert</span>
            </div>
        </div>
    </div>

    <!-- Right Sliding Panel for Shipment Details (Hidden by default) -->
    <div id="shipment-panel" class="position-absolute top-0 end-0 h-100 glass-panel border-start border-secondary border-opacity-25 z-2" style="width: 400px; transform: translateX(100%); transition: transform 0.3s ease-in-out; pointer-events: auto;">
        <div class="p-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
            <h5 class="text-white fw-bold mb-0">Vessel Intelligence</h5>
            <button class="btn btn-link text-muted p-0 hover-neon-text" onclick="closeShipmentPanel()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-4 overflow-auto h-100" id="shipment-panel-content">
            <!-- Dynamic Content loaded via AJAX -->
            <div class="text-center text-muted mt-5">
                <span class="spinner-border spinner-border-sm mb-2"></span>
                <p>Loading telemetry...</p>
            </div>
        </div>
    </div>
</main>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let map, shipmentMarkers = [], portMarkers = [];

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Map
        map = L.map('world-map', {
            zoomControl: false,
            attributionControl: false
        }).setView([20, 0], 3);

        // Dark Theme Tile Layer
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        // Add Zoom Control to Bottom Right
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Start Live Tracking loop
        fetchMapData();
        setInterval(fetchMapData, 30000); // Refresh every 30 seconds
    });

    function fetchMapData() {
        fetch('{{ route("map.data") }}')
            .then(response => response.json())
            .then(data => {
                renderShipments(data.shipments);
                renderPorts(data.ports);
            })
            .catch(error => console.error('Map Data Error:', error));
    }

    function renderShipments(shipments) {
        // Clear old markers
        shipmentMarkers.forEach(m => map.removeLayer(m));
        shipmentMarkers = [];

        // Custom Icon for Shipments
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
            const icon = ship.status === 'Redirected' ? redirectedIcon : shipIcon;
            const marker = L.marker([ship.lat, ship.lng], { icon: icon }).addTo(map);
            
            marker.on('click', () => openShipmentPanel(ship));
            shipmentMarkers.push(marker);
        });
    }

    function renderPorts(ports) {
        portMarkers.forEach(m => map.removeLayer(m));
        portMarkers = [];

        const portIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background: transparent; color: white; border: 2px solid rgba(255,255,255,0.5); border-radius: 4px; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                    <span class="material-symbols-outlined" style="font-size: 14px;">anchor</span>
                   </div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        ports.forEach(port => {
            const marker = L.marker([port.lat, port.lng], { icon: portIcon }).addTo(map);
            marker.bindTooltip(`<b>${port.name}</b><br>Congestion: ${port.congestion}`);
            portMarkers.push(marker);
        });
    }

    function openShipmentPanel(ship) {
        const panel = document.getElementById('shipment-panel');
        const content = document.getElementById('shipment-panel-content');
        
        panel.style.transform = 'translateX(0)';
        
        content.innerHTML = `
            <div class="mb-4">
                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 mb-2">${ship.status}</span>
                <h4 class="text-white fw-bold mb-0">${ship.shipment_number}</h4>
                <p class="text-muted fs-7">${ship.commodity}</p>
            </div>

            <div class="glass-pill p-3 mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-8">Speed</span>
                    <span class="text-white fw-bold fs-7">${ship.speed} knots</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-8">Heading</span>
                    <span class="text-white fw-bold fs-7">${ship.heading}°</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted fs-8">Weather</span>
                    <span class="text-warning fw-bold fs-7">${ship.weather}</span>
                </div>
            </div>

            <h6 class="text-white fw-bold mb-3">Route Information</h6>
            <div class="position-relative ms-2 mb-4">
                <div class="position-absolute h-100 border-start border-secondary" style="left: 4px; top: 10px; z-index: 0;"></div>
                
                <div class="d-flex gap-3 mb-3 position-relative" style="z-index: 1;">
                    <div class="rounded-circle bg-secondary mt-1" style="width: 10px; height: 10px;"></div>
                    <div>
                        <p class="text-white fw-bold mb-0 fs-7">${ship.origin}</p>
                        <span class="text-muted fs-8">Origin</span>
                    </div>
                </div>

                <div class="d-flex gap-3 position-relative" style="z-index: 1;">
                    <div class="rounded-circle bg-cyan-glow mt-1" style="width: 10px; height: 10px; background-color: var(--cyan-glow); box-shadow: 0 0 5px var(--cyan-glow);"></div>
                    <div>
                        <p class="text-white fw-bold mb-0 fs-7">${ship.destination}</p>
                        <span class="text-muted fs-8">Destination (ETA: ${ship.eta})</span>
                    </div>
                </div>
            </div>

            <a href="${ship.redirect_url}" class="text-decoration-none">
                <button class="btn btn-primary w-100 rounded-pill d-flex align-items-center justify-content-center gap-2" style="background-color: var(--purple-neon); border-color: var(--purple-neon);">
                    <span class="material-symbols-outlined fs-6">alt_route</span>
                    Smart Redirect
                </button>
            </a>
        `;
    }

    function closeShipmentPanel() {
        const panel = document.getElementById('shipment-panel');
        panel.style.transform = 'translateX(100%)';
    }
</script>

<style>
    /* Custom scrollbar and glass panel z-indexes */
    .leaflet-control-zoom {
        border: none !important;
        box-shadow: 0 0 15px rgba(0,0,0,0.5) !important;
    }
    .leaflet-control-zoom-in, .leaflet-control-zoom-out {
        background: rgba(13, 25, 48, 0.8) !important;
        color: white !important;
        border-color: rgba(255,255,255,0.1) !important;
        backdrop-filter: blur(10px);
    }
    .leaflet-control-zoom-in:hover, .leaflet-control-zoom-out:hover {
        background: rgba(79, 140, 255, 0.2) !important;
    }
</style>
@endsection
