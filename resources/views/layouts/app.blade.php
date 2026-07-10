<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Supply Chain Risk Intelligence Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    
    <!-- Animated Aurora Background -->
    <div class="animated-bg"></div>

    @if(request()->routeIs('dashboard'))
    <!-- Map Container (Only on Dashboard) -->
    <div class="map-container">
        <div id="world-map"></div>
    </div>
    @endif

    <!-- Main Floating Layout -->
    <div class="app-layout">
        
        <!-- Top Navigation -->
        <nav class="top-nav glass-panel">
            <div class="d-flex align-items-center">
                <span class="material-symbols-outlined text-cyan-glow me-2 fs-3" style="color: var(--cyan-glow);">public</span>
                <span class="fs-5 fw-bold text-white tracking-tight">GLOBAL<span style="color: var(--cyan-glow);">CHAIN</span></span>
            </div>
            
            <div class="glass-pill search-global">
                <span class="material-symbols-outlined text-muted fs-5">search</span>
                <input type="text" placeholder="Search Country, Commodity, Port, News...">
            </div>
            
            <div class="d-flex align-items-center gap-4">
                <div class="d-flex flex-column text-end d-none d-lg-flex">
                    <span class="fw-bold text-white lh-1" id="current-time">00:00:00 UTC</span>
                    <span class="fs-8 text-muted lh-1 mt-1" id="current-date">Jan 01, 2026</span>
                </div>

                <div class="d-flex align-items-center gap-3 border-start border-secondary ps-4">
                    <button class="btn btn-link text-muted p-0 text-decoration-none hover-neon-text">
                        <span class="material-symbols-outlined fs-5">dark_mode</span>
                    </button>
                    <button class="btn btn-link text-muted p-0 text-decoration-none hover-neon-text">
                        <span class="material-symbols-outlined fs-5">translate</span>
                    </button>
                    <button class="btn btn-link text-muted p-0 position-relative text-decoration-none hover-neon-text">
                        <span class="material-symbols-outlined fs-5">notifications</span>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger rounded-circle"></span>
                    </button>
                </div>
                
                <div class="dropdown">
                    <div class="d-flex align-items-center cursor-pointer dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-6" style="width: 36px; height: 36px; background-color: var(--electric-blue); box-shadow: 0 0 15px var(--electric-blue);">
                            {{ Auth::user() ? substr(Auth::user()->name, 0, 1) : 'G' }}
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow border-secondary mt-2 glass-panel">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Settings</a></li>
                        <li><hr class="dropdown-divider border-secondary"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') ?? '#' }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Middle Section -->
        <div class="workspace-area">
            
            <!-- Floating Sidebar -->
            <aside class="left-sidebar glass-panel">
                <div class="sidebar-logo-icon">
                    <span class="material-symbols-outlined fs-2">apps</span>
                </div>
                
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('dashboard') ?? '#' }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">dashboard</span>
                            <span class="text">Command Center</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('shipments.index') ?? '#' }}" class="{{ request()->routeIs('shipments.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">local_shipping</span>
                            <span class="text">Shipment Intel</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('map.index') }}" class="{{ request()->routeIs('map.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">explore</span>
                            <span class="text">World Map</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('intelligence.index') }}" class="{{ request()->routeIs('intelligence.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">language</span>
                            <span class="text">Global Intelligence</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('intelligence.ports') }}">
                            <span class="material-symbols-outlined icon">anchor</span>
                            <span class="text">Port Intel</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('intelligence.commodities') }}">
                            <span class="material-symbols-outlined icon">inventory_2</span>
                            <span class="text">Commodities</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="material-symbols-outlined icon">payments</span>
                            <span class="text">Currencies</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="material-symbols-outlined icon">storm</span>
                            <span class="text">Weather Intel</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="material-symbols-outlined icon text-warning">security</span>
                            <span class="text text-warning">Risk Alerts</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="material-symbols-outlined icon">trending_up</span>
                            <span class="text">Trade Analytics</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="material-symbols-outlined icon">newspaper</span>
                            <span class="text">Global News</span>
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Dynamic Content (Yields Dashboard or Other Views) -->
            @yield('content')
            
        </div>

        <!-- Bottom Timeline Ticker -->
        <footer class="bottom-ticker-container glass-panel">
            <div class="ticker-track">
                <!-- Original Set -->
                <div class="timeline-event">
                    <span class="material-symbols-outlined fs-6 text-success me-2">check_circle</span>
                    <span class="fw-bold text-white me-2">10:42 UTC</span> Shipment #SHP-9021 arrived at Port of Shanghai.
                </div>
                <div class="timeline-event">
                    <span class="material-symbols-outlined fs-6 text-warning me-2">warning</span>
                    <span class="fw-bold text-white me-2">10:35 UTC</span> AI Alert: High port congestion detected in Rotterdam.
                </div>
                <div class="timeline-event">
                    <span class="material-symbols-outlined fs-6 text-cyan-glow me-2" style="color: var(--cyan-glow);">sailing</span>
                    <span class="fw-bold text-white me-2">10:15 UTC</span> Vessel MSC Isabella updated route to avoid storm.
                </div>
                <div class="timeline-event">
                    <span class="material-symbols-outlined fs-6 text-danger me-2">gpp_bad</span>
                    <span class="fw-bold text-white me-2">09:50 UTC</span> Risk level elevated for Suez Canal transit.
                </div>
                <div class="timeline-event">
                    <span class="material-symbols-outlined fs-6 text-purple-neon me-2" style="color: var(--purple-neon);">show_chart</span>
                    <span class="fw-bold text-white me-2">08:15 UTC</span> Global Copper prices dropped by 2.4%.
                </div>
                
                <!-- Duplicated Set for Loop -->
                <div class="timeline-event">
                    <span class="material-symbols-outlined fs-6 text-success me-2">check_circle</span>
                    <span class="fw-bold text-white me-2">10:42 UTC</span> Shipment #SHP-9021 arrived at Port of Shanghai.
                </div>
                <div class="timeline-event">
                    <span class="material-symbols-outlined fs-6 text-warning me-2">warning</span>
                    <span class="fw-bold text-white me-2">10:35 UTC</span> AI Alert: High port congestion detected in Rotterdam.
                </div>
                <div class="timeline-event">
                    <span class="material-symbols-outlined fs-6 text-cyan-glow me-2" style="color: var(--cyan-glow);">sailing</span>
                    <span class="fw-bold text-white me-2">10:15 UTC</span> Vessel MSC Isabella updated route to avoid storm.
                </div>
                <div class="timeline-event">
                    <span class="material-symbols-outlined fs-6 text-danger me-2">gpp_bad</span>
                    <span class="fw-bold text-white me-2">09:50 UTC</span> Risk level elevated for Suez Canal transit.
                </div>
                <div class="timeline-event">
                    <span class="material-symbols-outlined fs-6 text-purple-neon me-2" style="color: var(--purple-neon);">show_chart</span>
                    <span class="fw-bold text-white me-2">08:15 UTC</span> Global Copper prices dropped by 2.4%.
                </div>
            </div>
        </footer>
        
    </div>

    <!-- Floating AI Assistant -->
    <div class="ai-assistant-orb">
        <span class="material-symbols-outlined text-white fs-2">smart_toy</span>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Clock
        function updateClock() {
            const now = new Date();
            const timeEl = document.getElementById('current-time');
            if(timeEl) {
                timeEl.innerText = 
                    now.getUTCHours().toString().padStart(2, '0') + ':' + 
                    now.getUTCMinutes().toString().padStart(2, '0') + ':' + 
                    now.getUTCSeconds().toString().padStart(2, '0') + ' UTC';
                    
                const options = { month: 'short', day: '2-digit', year: 'numeric' };
                document.getElementById('current-date').innerText = now.toLocaleDateString('en-US', options);
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
