@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    
    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="text-white fw-bold tracking-tight mb-1">
                {{ $shipment->shipment_number }}
            </h3>
            <div class="d-flex gap-2 align-items-center">
                @if($shipment->status === 'Redirected')
                    <x-badge variant="purple" icon="alt_route">{{ $shipment->status }}</x-badge>
                @elseif($shipment->status === 'Pending')
                    <x-badge variant="warning" icon="hourglass_empty">{{ $shipment->status }}</x-badge>
                @else
                    <x-badge variant="success" icon="sailing">{{ $shipment->status }}</x-badge>
                @endif
                <span class="text-muted fs-7">{{ ucfirst($shipment->type) }} • {{ $shipment->commodity }}</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('shipments.index') }}" class="text-decoration-none">
                <x-button variant="outline" icon="arrow_back">Back</x-button>
            </a>
            @if($shipment->status === 'Pending')
                <form action="{{ route('shipments.start', $shipment) }}" method="POST" class="d-inline">
                    @csrf
                    <x-button type="submit" variant="success" icon="sailing" style="background-color: var(--success); color: white;">Start Voyage</x-button>
                </form>
            @endif
            @if(in_array($shipment->status, ['Pending', 'Preparing', 'Loading', 'Departed', 'In Transit', 'Delayed']))
                <a href="{{ route('shipments.redirect', $shipment) }}" class="text-decoration-none">
                    <x-button variant="primary" icon="alt_route" class="bg-purple-neon border-purple hover-neon-text" style="background-color: var(--purple-neon); color: white;">Smart Redirect</x-button>
                </a>
            @endif
        </div>
    </div>

    <!-- Smart Redirect Recommendation AI Alert (Mockup logic for visual) -->
    @if($shipment->status !== 'Redirected' && $shipment->destination_country === 'United States')
        <x-alert variant="warning" icon="smart_toy">
            <h6 class="fw-bold mb-1">AI Recommendation: Port Congestion Alert</h6>
            <p class="mb-2 fs-7 text-white">High congestion detected at {{ $shipment->destination_port }}. Redirecting to Port of Savannah could save 4 days of waiting time.</p>
            <a href="{{ route('shipments.redirect', $shipment) }}" class="text-decoration-none text-warning fw-bold fs-7">Analyze Redirect →</a>
        </x-alert>
    @endif

    <div class="row g-4">
        
        <!-- Map & Route Information -->
        <div class="col-md-8 d-flex flex-column gap-4">
            <x-card title="Live Tracking & Route" icon="explore" glow="cyan">
                <!-- Map Container -->
                <div id="shipment-detail-map" class="w-100 rounded-3 mb-4 border border-secondary border-opacity-25" style="height: 300px; background: rgba(14, 165, 233, 0.05); overflow: hidden; position: relative; z-index: 1;">
                </div>

                <div class="row g-3 px-3 pb-3">
                    <div class="col-md-5">
                        <p class="text-muted mb-1 fs-7">Origin</p>
                        <h6 class="text-white fw-bold mb-0">{{ $shipment->origin_country }}</h6>
                        <span class="text-muted fs-8">{{ $shipment->origin_port }}</span>
                    </div>
                    <div class="col-md-2 d-flex justify-content-center align-items-center">
                        <span class="material-symbols-outlined text-muted fs-3">arrow_forward</span>
                    </div>
                    <div class="col-md-5 text-end">
                        <p class="text-muted mb-1 fs-7">Destination</p>
                        <h6 class="text-white fw-bold mb-0">{{ $shipment->destination_country }}</h6>
                        <span class="text-muted fs-8">{{ $shipment->destination_port }}</span>
                        @php
                            if ($shipment->estimated_arrival) {
                                $targetDate = \Carbon\Carbon::parse($shipment->estimated_arrival);
                                $days = abs((int) now()->diffInDays($targetDate, false));
                            } else {
                                $days = rand(8, 24);
                            }
                        @endphp
                        <br><span class="badge bg-primary bg-opacity-25 mt-1" style="color: var(--cyan-glow); border: 1px solid var(--cyan-glow);">ETA: {{ $days }} Days</span>
                    </div>
                </div>
            </x-card>

            <!-- Redirection History -->
            @if($shipment->redirects->count() > 0)
                <x-card title="Redirection History" icon="history" glow="purple">
                    <div class="p-3">
                        @foreach($shipment->redirects as $redirect)
                            <div class="glass-pill p-3 mb-3 border border-purple border-opacity-25">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-white fw-bold">Redirected to {{ $redirect->new_destination_country }}</span>
                                    <span class="text-muted fs-8">{{ $redirect->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="text-muted fs-8">From:</span>
                                    <span class="text-white fs-8">{{ $redirect->old_destination_port }}</span>
                                    <span class="material-symbols-outlined text-muted fs-8">arrow_forward</span>
                                    <span class="text-white fs-8">{{ $redirect->new_destination_port }}</span>
                                </div>
                                <div class="p-2 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded mt-2">
                                    <span class="text-danger fs-8 fw-bold">Reason:</span>
                                    <span class="text-white fs-8">{{ $redirect->reason }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif
        </div>

        <!-- Right Panel: Cargo Intel & Timeline -->
        <div class="col-md-4 d-flex flex-column gap-4">
            
            <x-card title="Cargo Manifesto" icon="inventory_2" glow="purple">
                <div class="p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-purple bg-opacity-25 p-2 me-3 d-flex align-items-center justify-content-center">
                            <span class="material-symbols-outlined text-purple">category</span>
                        </div>
                        <div>
                            <p class="text-muted fs-8 mb-0">Primary Commodity</p>
                            <h5 class="text-white fw-bold mb-0">{{ $shipment->commodity }}</h5>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-dark border border-secondary border-opacity-25 rounded p-2">
                                <span class="text-muted fs-8 d-block mb-1">Volume/Weight</span>
                                <span class="text-white fw-bold fs-7">{{ $shipment->quantity }} {{ $shipment->weight_unit }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-dark border border-secondary border-opacity-25 rounded p-2">
                                <span class="text-muted fs-8 d-block mb-1">Vessel Name</span>
                                <span class="text-white fw-bold fs-7">{{ $shipment->vessel_name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>

            <x-card title="Cargo Financials" icon="analytics" glow="success">
                <div class="p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fs-7">Estimated Revenue</span>
                        <span class="text-success fw-bold">${{ number_format($shipment->estimated_revenue ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fs-7">Estimated Profit</span>
                        <span class="text-success fw-bold">${{ number_format($shipment->estimated_profit ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-top border-secondary border-opacity-25 pt-2 mt-2">
                        <span class="text-muted fs-7">Shipping Cost</span>
                        <span class="text-danger fw-bold">-${{ number_format($shipment->shipping_cost ?? 0, 2) }}</span>
                    </div>
                </div>
            </x-card>

            <x-card title="Activity Timeline" icon="timeline">
                <div class="p-3 position-relative">
                    <!-- Vertical Line -->
                    <div class="position-absolute h-100 border-start border-secondary" style="left: 28px; top: 15px; z-index: 0;"></div>
                    
                    @forelse($shipment->activities as $activity)
                        <div class="d-flex gap-3 mb-4 position-relative" style="z-index: 1;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-dark border border-secondary mt-1 flex-shrink-0" style="width: 28px; height: 28px;">
                                <div class="rounded-circle bg-cyan-glow" style="width: 8px; height: 8px; background-color: var(--cyan-glow); box-shadow: 0 0 5px var(--cyan-glow);"></div>
                            </div>
                            <div>
                                <h6 class="text-white fw-bold mb-1">{{ $activity->status }}</h6>
                                <p class="text-muted fs-8 mb-1">{{ $activity->description }}</p>
                                <span class="text-secondary fs-8">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted fs-7">No activities recorded yet.</p>
                    @endforelse
                </div>
            </x-card>

        </div>
    </div>
</main>
@endsection

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- SeaRoute JS -->
<script src="https://cdn.jsdelivr.net/npm/searoute-js@0.1.0/searoute.min.js"></script>

<style>
    .animated-route { stroke-dasharray: 8, 8; animation: dash 20s linear infinite; }
    @keyframes dash { to { stroke-dashoffset: -1000; } }
    .custom-div-icon { background: transparent; border: none; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Map
        const map = L.map('shipment-detail-map', {
            zoomControl: false,
            attributionControl: false
        }).setView([20, 0], 2);

        L.tileLayer(
            document.documentElement.getAttribute('data-theme') === 'light' 
                ? 'https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png'
                : 'https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png', 
            { subdomains: 'abcd', maxZoom: 19 }
        ).addTo(map);

        // === PREDEFINED HIGHLY DETAILED REALISTIC ROUTES ===
        const seaRoutes = {
            'shanghai-rotterdam': [[31.228, 121.475], [24.5, 119.5], [15.0, 112.0], [1.2, 104.0], [5.0, 98.0], [5.8, 80.0], [12.0, 60.0], [12.0, 45.0], [14.0, 42.5], [20.0, 39.0], [27.0, 34.5], [30.0, 32.5], [31.5, 32.2], [34.0, 25.0], [36.0, 15.0], [37.5, 5.0], [36.0, -5.0], [38.0, -10.0], [45.0, -8.0], [49.0, -4.0], [50.5, 0.0], [51.949, 4.148]],
            'shanghai-hamburg': [[31.228, 121.475], [24.5, 119.5], [15.0, 112.0], [1.2, 104.0], [5.0, 98.0], [5.8, 80.0], [12.0, 60.0], [12.0, 45.0], [14.0, 42.5], [20.0, 39.0], [27.0, 34.5], [30.0, 32.5], [31.5, 32.2], [34.0, 25.0], [36.0, 15.0], [37.5, 5.0], [36.0, -5.0], [38.0, -10.0], [45.0, -8.0], [49.0, -4.0], [52.0, 3.0], [53.548, 9.987]],
            'shanghai-yokohama': [[31.228, 121.475], [31.0, 125.0], [30.5, 130.0], [32.0, 134.0], [34.0, 138.5], [35.1, 139.7], [35.443, 139.638]],
            'shanghai-cape town': [[31.228, 121.475], [24.5, 119.5], [15.0, 112.0], [1.2, 104.0], [5.0, 98.0], [0.0, 90.0], [-15.0, 70.0], [-25.0, 50.0], [-35.0, 35.0], [-36.0, 20.0], [-33.924, 18.424]],
            'shanghai-savannah': [[31.228, 121.475], [30.0, 130.0], [20.0, 160.0], [15.0, -160.0], [7.5, -81.0], [8.9, -79.5], [9.3, -79.9], [15.0, -75.0], [25.0, -79.0], [28.0, -80.0], [32.08, -81.09]],
            'los angeles-yokohama': [[33.728, -118.262], [34.0, -130.0], [35.0, -150.0], [36.0, -170.0], [36.0, 170.0], [35.0, 150.0], [35.443, 139.638]],
            'santos-shanghai': [[-23.953, -46.335], [-30.0, -30.0], [-35.0, -10.0], [-35.5, 19.5], [-30.0, 40.0], [-20.0, 60.0], [-10.0, 80.0], [-6.0, 105.0], [-4.0, 108.0], [-2.0, 108.5], [5.0, 109.0], [15.0, 115.0], [24.0, 119.0], [31.228, 121.475]],
            'santos-yokohama': [[-23.953, -46.335], [-30.0, -30.0], [-35.0, -10.0], [-35.5, 19.5], [-30.0, 40.0], [-20.0, 60.0], [-10.0, 80.0], [-6.0, 105.0], [-4.0, 108.0], [-2.0, 108.5], [5.0, 109.0], [15.0, 115.0], [24.0, 119.0], [31.228, 121.475], [31.0, 125.0], [30.5, 130.0], [32.0, 134.0], [34.0, 138.5], [35.443, 139.638]],
            'jebel ali-hamburg': [[24.985, 55.027], [26.5, 56.5], [24.0, 59.0], [15.0, 55.0], [12.0, 45.0], [14.0, 42.5], [20.0, 39.0], [27.0, 34.5], [30.0, 32.5], [31.5, 32.2], [34.0, 25.0], [36.0, 15.0], [37.5, 5.0], [36.0, -5.0], [38.0, -10.0], [45.0, -8.0], [49.0, -4.0], [52.0, 3.0], [53.548, 9.987]],
            'new york-cape town': [[40.678, -73.998], [35.0, -65.0], [20.0, -45.0], [0.0, -25.0], [-15.0, -10.0], [-25.0, 0.0], [-33.924, 18.424]],
            'jebel ali-yokohama': [[24.985, 55.027], [26.5, 56.5], [20.0, 60.0], [5.8, 80.0], [5.0, 98.0], [1.2, 104.0], [10.0, 110.0], [22.0, 120.0], [30.0, 130.0], [34.0, 138.5], [35.1, 139.7], [35.443, 139.638]],
            'jebel ali-shanghai': [[24.985, 55.027], [26.5, 56.5], [20.0, 60.0], [5.8, 80.0], [5.0, 98.0], [1.2, 104.0], [15.0, 112.0], [24.5, 119.5], [31.228, 121.475]],
            'sydney-dubrovnik': [[-33.868, 151.209], [-40.0, 140.0], [-38.0, 120.0], [-25.0, 100.0], [-10.0, 80.0], [5.0, 65.0], [12.0, 45.0], [14.0, 42.5], [20.0, 39.0], [27.0, 34.5], [30.0, 32.5], [31.5, 32.2], [34.0, 25.0], [36.0, 20.0], [40.0, 18.0], [42.65, 18.09]]
        };

        // === ADVANCED GRAPH-BASED SEA ROUTING (Fallback) ===
        // Define key maritime nodes (ports + ocean waypoints) to route around land
        const oceanNodes = {
            'shanghai': [31.228, 121.475],
            'rotterdam': [51.949, 4.148],
            'los angeles': [33.728, -118.262],
            'yokohama': [35.443, 139.638],
            'santos': [-23.953, -46.335],
            'jebel ali': [24.985, 55.027],
            'hamburg': [53.548, 9.987],
            'new york': [40.678, -73.998],
            'cape town': [-33.924, 18.424],
            'savannah': [32.08, -81.09],
            'sydney': [-33.868, 151.209],
            'dubrovnik': [42.65, 18.09],
            'mumbai': [18.944, 72.836],
            'london': [51.507, 0.127],
            
            // Coast-hugging Waypoints
            'wp_malacca': [5.0, 98.0],
            'wp_singapore': [1.2, 104.0],
            'wp_south_china_sea': [15.0, 115.0],
            'wp_taiwan_strait': [24.0, 119.0],
            'wp_sri_lanka': [5.0, 80.0],
            'wp_gulf_of_aden': [12.0, 45.0],
            'wp_red_sea': [20.0, 39.0],
            'wp_suez': [27.5, 34.0],
            'wp_mediterranean': [35.0, 15.0],
            'wp_gibraltar': [35.9, -5.5],
            'wp_portugal_coast': [38.0, -10.0],
            'wp_biscay': [45.0, -8.0],
            'wp_english_channel': [50.0, -3.0],
            'wp_us_east': [35.0, -74.0],
            'wp_caribbean': [20.0, -74.0],
            'wp_panama': [9.0, -79.5],
            'wp_mexico_coast': [15.0, -100.0],
            'wp_brazil_coast': [-10.0, -32.0],
            'wp_argentina_coast': [-40.0, -55.0],
            'wp_cape_horn': [-56.0, -67.0],
            'wp_cape_good_hope': [-36.0, 18.0],
            'wp_south_madagascar': [-35.0, 45.0],
            'wp_east_africa': [-10.0, 45.0],
            'wp_indian_ocean': [-10.0, 75.0],
            'wp_sunda': [-6.0, 105.0],
            'wp_arafura': [-10.0, 135.0],
            'wp_coral_sea': [-15.0, 155.0],
            'wp_atlantic_mid': [0.0, -30.0],
            'wp_atlantic_south': [-30.0, -15.0],
            'wp_pacific_mid': [0.0, -150.0],
            'wp_arabian': [15.0, 60.0]
        };

        const oceanEdges = {
            'shanghai': ['wp_taiwan_strait', 'yokohama'],
            'yokohama': ['shanghai', 'wp_pacific_mid', 'wp_taiwan_strait'],
            'wp_taiwan_strait': ['shanghai', 'yokohama', 'wp_south_china_sea'],
            'wp_south_china_sea': ['wp_taiwan_strait', 'wp_singapore'],
            'wp_singapore': ['wp_south_china_sea', 'wp_malacca', 'wp_sunda'],
            'wp_sunda': ['wp_singapore', 'wp_indian_ocean', 'wp_arafura'],
            'wp_arafura': ['wp_sunda', 'wp_coral_sea'],
            'wp_coral_sea': ['wp_arafura', 'sydney', 'wp_pacific_mid'],
            'sydney': ['wp_coral_sea', 'wp_pacific_mid'],
            'wp_malacca': ['wp_singapore', 'wp_sri_lanka'],
            'wp_sri_lanka': ['wp_malacca', 'mumbai', 'wp_indian_ocean', 'wp_gulf_of_aden'],
            'mumbai': ['wp_sri_lanka', 'wp_arabian'],
            'wp_arabian': ['mumbai', 'jebel ali', 'wp_gulf_of_aden'],
            'jebel ali': ['wp_arabian'],
            'wp_gulf_of_aden': ['wp_arabian', 'wp_sri_lanka', 'wp_red_sea', 'wp_east_africa'],
            'wp_indian_ocean': ['wp_sri_lanka', 'wp_sunda', 'wp_south_madagascar', 'wp_east_africa'],
            'wp_east_africa': ['wp_indian_ocean', 'wp_gulf_of_aden', 'wp_south_madagascar'],
            'wp_south_madagascar': ['wp_east_africa', 'wp_indian_ocean', 'wp_cape_good_hope'],
            'wp_red_sea': ['wp_gulf_of_aden', 'wp_suez'],
            'wp_suez': ['wp_red_sea', 'wp_mediterranean'],
            'wp_mediterranean': ['wp_suez', 'dubrovnik', 'wp_gibraltar'],
            'dubrovnik': ['wp_mediterranean'],
            'wp_gibraltar': ['wp_mediterranean', 'wp_portugal_coast', 'wp_atlantic_mid'],
            'wp_portugal_coast': ['wp_gibraltar', 'wp_biscay'],
            'wp_biscay': ['wp_portugal_coast', 'wp_english_channel'],
            'wp_english_channel': ['wp_biscay', 'london', 'rotterdam'],
            'london': ['wp_english_channel', 'rotterdam', 'hamburg'],
            'rotterdam': ['wp_english_channel', 'london', 'hamburg'],
            'hamburg': ['rotterdam', 'london'],
            'new york': ['wp_us_east', 'wp_atlantic_mid'],
            'wp_us_east': ['new york', 'savannah'],
            'savannah': ['wp_us_east', 'wp_caribbean'],
            'wp_caribbean': ['savannah', 'wp_panama', 'wp_atlantic_mid'],
            'wp_atlantic_mid': ['wp_gibraltar', 'new york', 'wp_caribbean', 'wp_atlantic_south', 'wp_brazil_coast'],
            'wp_brazil_coast': ['wp_atlantic_mid', 'santos'],
            'wp_atlantic_south': ['wp_atlantic_mid', 'santos', 'wp_cape_good_hope', 'cape town'],
            'santos': ['wp_brazil_coast', 'wp_atlantic_south', 'wp_argentina_coast'],
            'wp_argentina_coast': ['santos', 'wp_cape_horn'],
            'cape town': ['wp_atlantic_south', 'wp_cape_good_hope'],
            'wp_cape_good_hope': ['cape town', 'wp_atlantic_south', 'wp_south_madagascar'],
            'wp_cape_horn': ['wp_argentina_coast', 'wp_pacific_mid'],
            'wp_panama': ['wp_caribbean', 'wp_mexico_coast'],
            'wp_mexico_coast': ['wp_panama', 'los angeles'],
            'los angeles': ['wp_mexico_coast', 'wp_pacific_mid', 'yokohama'],
            'wp_pacific_mid': ['los angeles', 'yokohama', 'wp_coral_sea', 'sydney', 'wp_cape_horn']
        };

        // Use Haversine formula for actual earth surface distance to prevent high-latitude distortion
        function getDist(p1, p2) {
            const R = 6371;
            const dLat = (p2[0] - p1[0]) * Math.PI / 180;
            let dLng = (p2[1] - p1[1]);
            if (Math.abs(dLng) > 180) dLng = (360 - Math.abs(dLng)) * Math.sign(-dLng);
            dLng = dLng * Math.PI / 180;
            const lat1 = p1[0] * Math.PI / 180;
            const lat2 = p2[0] * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.sin(dLng/2) * Math.sin(dLng/2) * Math.cos(lat1) * Math.cos(lat2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        function findClosestNode(targetCoord) {
            let minD = Infinity;
            let closest = null;
            for (let [name, coord] of Object.entries(oceanNodes)) {
                let d = getDist(targetCoord, coord);
                if (d < minD) {
                    minD = d;
                    closest = name;
                }
            }
            return closest;
        }

        function findGraphRoute(startNode, endNode) {
            let nodes = new Set(Object.keys(oceanNodes));
            let distances = {}, prev = {};
            for (let n of nodes) { distances[n] = Infinity; prev[n] = null; }
            distances[startNode] = 0;

            while (nodes.size > 0) {
                let curr = null, minD = Infinity;
                for (let n of nodes) {
                    if (distances[n] < minD) { minD = distances[n]; curr = n; }
                }
                if (curr === null || curr === endNode) break;
                nodes.delete(curr);

                for (let neighbor of (oceanEdges[curr] || [])) {
                    if (nodes.has(neighbor)) {
                        let d = distances[curr] + getDist(oceanNodes[curr], oceanNodes[neighbor]);
                        if (d < distances[neighbor]) { distances[neighbor] = d; prev[neighbor] = curr; }
                    }
                }
            }
            let path = [], curr = endNode;
            while (curr) { path.unshift(oceanNodes[curr]); curr = prev[curr]; }
            return path;
        }

        // Determine origin and current destination
        const isRedirected = '{{ $shipment->status }}' === 'Redirected';
        const color = isRedirected ? 'var(--purple-neon)' : 'var(--cyan-glow)';
        
        // Ensure coordinates are mapped properly
        let originCoord = [{{ $originPort ? $originPort->latitude : 0 }}, {{ $originPort ? $originPort->longitude : 0 }}];
        let destCoord = [{{ $destPort ? $destPort->latitude : 0 }}, {{ $destPort ? $destPort->longitude : 0 }}];
        
        let originalOrigin = '{{ strtolower(str_replace("Port of ", "", $shipment->origin_port)) }}';
        let destPort = '{{ strtolower(str_replace("Port of ", "", $shipment->destination_port)) }}';
        
        const routeKey = originalOrigin + '-' + destPort;
        const reversedRouteKey = destPort + '-' + originalOrigin;

        let routePoints = [];

        // Check if we have a highly detailed predefined sea route
        if (seaRoutes[routeKey]) {
            routePoints = [...seaRoutes[routeKey]];
        } else if (seaRoutes[reversedRouteKey]) {
            routePoints = [...seaRoutes[reversedRouteKey]].reverse();
        } else {
            // Fallback to Graph Routing
            if (originCoord[0] === 0 && originCoord[1] === 0) {
                originCoord = oceanNodes[originalOrigin] || oceanNodes['yokohama'];
            }
            if (destCoord[0] === 0 && destCoord[1] === 0) {
                destCoord = oceanNodes[destPort] || oceanNodes['shanghai'];
            }

            let startNode = oceanNodes[originalOrigin] ? originalOrigin : findClosestNode(originCoord);
            let endNode = oceanNodes[destPort] ? destPort : findClosestNode(destCoord);
            
            routePoints.push(originCoord);
            
            if (startNode !== endNode) {
                let graphPath = findGraphRoute(startNode, endNode);
                for (let pt of graphPath) {
                    let lastPt = routePoints[routePoints.length - 1];
                    let fixedLng = pt[1];
                    if (lastPt[1] - fixedLng > 180) fixedLng += 360;
                    else if (fixedLng - lastPt[1] > 180) fixedLng -= 360;
                    routePoints.push([pt[0], fixedLng]);
                }
            }
            
            let lastPt = routePoints[routePoints.length - 1];
            let finalLng = destCoord[1];
            if (lastPt[1] - finalLng > 180) finalLng += 360;
            else if (finalLng - lastPt[1] > 180) finalLng -= 360;
            routePoints.push([destCoord[0], finalLng]);
        }

        // Draw Route
        const routeLine = L.polyline(routePoints, {
            color: color,
            weight: 3,
            opacity: 0.8,
            className: 'animated-route'
        }).addTo(map);

        // Fit Bounds to show the entire route
        map.fitBounds(routeLine.getBounds(), { padding: [20, 20] });

        // Add Ship Marker somewhere in the middle
        const shipIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background: ${color}; color: ${isRedirected ? 'white' : 'black'}; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px ${color};">
                    <span class="material-symbols-outlined" style="font-size: 18px;">${isRedirected ? 'alt_route' : 'sailing'}</span>
                   </div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });

        // Initialize marker
        let progress = 0.1; // start at 10%
        let shipSpeed = 0.000005; // Slowed down significantly (10x slower)
        const shipMarker = L.marker(routePoints[0], { icon: shipIcon }).addTo(map);
        
        // Add Port Origin & Dest Markers
        const portIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background: rgba(10,17,40,0.8); color: white; border: 2px solid var(--cyan-glow); border-radius: 4px; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                    <span class="material-symbols-outlined" style="font-size: 14px;">anchor</span>
                   </div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
        
        L.marker(routePoints[0], { icon: portIcon }).addTo(map);
        L.marker(routePoints[routePoints.length - 1], { icon: portIcon }).addTo(map);

        // Helper function for interpolation
        function getPointOnRoute(route, currentProgress) {
            if(!route || route.length < 2) return route && route[0] ? route[0] : [0,0];
            let totalSegments = route.length - 1;
            let scaledProgress = currentProgress * totalSegments;
            let index = Math.floor(scaledProgress);
            if(index >= totalSegments) return route[totalSegments];
            
            let remainder = scaledProgress - index;
            let p1 = route[index];
            let p2 = route[index + 1];
            
            let lat = p1[0] + (p2[0] - p1[0]) * remainder;
            let lng = p1[1] + (p2[1] - p1[1]) * remainder;
            return [lat, lng];
        }

        // Animation Loop
        function animateShip() {
            progress += shipSpeed;
            if(progress > 1) progress = 0; // loop back
            
            let newLatLng = getPointOnRoute(routePoints, progress);
            shipMarker.setLatLng(newLatLng);
            
            requestAnimationFrame(animateShip);
        }

        // Start animation
        animateShip();
    });
</script>
