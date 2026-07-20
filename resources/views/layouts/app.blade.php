<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Supply Chain Risk Intelligence Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Hide Google Translate top bar and elements */
        .goog-te-banner-frame.skiptranslate, 
        .goog-te-banner-frame,
        #goog-gt-tt,
        .goog-te-balloon-frame,
        iframe.goog-te-menu-frame {
            display: none !important;
        }
        body {
            top: 0px !important;
        }
        .goog-text-highlight {
            background-color: transparent !important;
            box-shadow: none !important;
        }
        .no-caret::after {
            display: none !important;
        }
    </style>
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
                <span class="material-symbols-outlined text-cyan-glow me-2 fs-3" style="color: var(--cyan-glow);">hub</span>
                <span class="fs-5 fw-bold text-white tracking-tight notranslate" style="letter-spacing: 0.5px;">GLOBAL<span style="color: var(--cyan-glow); font-weight: 300;">CHAIN</span></span>
            </div>
            
            <form action="{{ route('search') }}" method="GET" class="glass-pill search-global m-0 d-flex align-items-center">
                <button type="submit" class="btn p-0 border-0 text-muted d-flex align-items-center" style="background: none;">
                    <span class="material-symbols-outlined fs-5">search</span>
                </button>
                <input type="text" name="q" placeholder="Search Country, Commodity, Port, News..." class="border-0 bg-transparent text-white shadow-none ms-2 w-100 placeholder-light" style="outline: none;">
            </form>
            
            <div class="d-flex align-items-center gap-4">
                <div class="d-flex flex-column text-end d-none d-lg-flex">
                    <span class="fw-bold text-white lh-1" id="current-time">00:00:00 UTC</span>
                    <span class="fs-8 text-muted lh-1 mt-1" id="current-date">Jan 01, 2026</span>
                </div>

                <div class="d-flex align-items-center gap-3 border-start border-secondary ps-4">
                    <button class="btn btn-link text-muted p-0 text-decoration-none hover-neon-text" onclick="toggleTheme()" id="theme-toggle-btn">
                        <span class="material-symbols-outlined fs-5">light_mode</span>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-link text-muted p-0 text-decoration-none hover-neon-text dropdown-toggle no-caret" data-bs-toggle="dropdown" aria-expanded="false" title="Translate Page">
                            <span class="material-symbols-outlined fs-5">translate</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow border-secondary mt-2 glass-panel" style="min-width: 175px;">
                            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" onclick="changeLanguage('en'); return false;">🇺🇸 English</a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" onclick="changeLanguage('id'); return false;">🇮🇩 Indonesia</a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" onclick="changeLanguage('es'); return false;">🇪🇸 Español</a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" onclick="changeLanguage('de'); return false;">🇩🇪 Deutsch</a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" onclick="changeLanguage('ja'); return false;">🇯🇵 日本語</a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" onclick="changeLanguage('zh-CN'); return false;">🇨🇳 中文 (简体)</a></li>
                        </ul>
                    </div>
                    <div id="google_translate_element" style="display:none; visibility:hidden; height:0; width:0; overflow:hidden;"></div>
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
                        <a href="{{ route('intelligence.index') }}" class="{{ request()->routeIs('intelligence.index') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">language</span>
                            <span class="text">Global Intelligence</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('ai.index') }}" class="menu-item-ai {{ request()->routeIs('ai.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon ai-icon">psychology</span>
                            <span class="text fw-bold ai-text">AI Decision Support</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('intelligence.ports') }}" class="{{ request()->routeIs('intelligence.ports') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">anchor</span>
                            <span class="text">Port Intel</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('intelligence.commodities') }}" class="{{ request()->routeIs('intelligence.commodities') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">inventory_2</span>
                            <span class="text">Commodities</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('intelligence.countries') }}" class="{{ request()->routeIs('intelligence.countries') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">public</span>
                            <span class="text">Global Country Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('intelligence.compare') }}" class="{{ request()->routeIs('intelligence.compare') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon text-purple-neon">compare_arrows</span>
                            <span class="text">Country Comparison Engine</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('intelligence.commodity-compare') }}" class="{{ request()->routeIs('intelligence.commodity-compare') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon text-info">balance</span>
                            <span class="text">Commodity Comparison</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('intelligence.exchange-rate') }}" class="{{ request()->routeIs('intelligence.exchange-rate') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">currency_exchange</span>
                            <span class="text">FX Exchange Rates</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('intelligence.risk-alerts') }}" class="{{ request()->routeIs('intelligence.risk-alerts') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon text-warning">security</span>
                            <span class="text text-warning">Risk Scoring Engine</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('analytics.index') }}" class="{{ request()->routeIs('analytics.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">trending_up</span>
                            <span class="text">Trade Analytics</span>
                        </a>
                    </li>
                    @if(Auth::check() && Auth::user()->role === 'admin')
                    <li>
                        <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon text-danger">admin_panel_settings</span>
                            <span class="text text-danger">Administration</span>
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('intelligence.news') }}" class="{{ request()->routeIs('intelligence.news') ? 'active' : '' }}">
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



    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Prevent Google Translate from translating material symbols icons (protect ligatures)
        const iconObserver = new MutationObserver(() => {
            document.querySelectorAll('.material-symbols-outlined').forEach(el => {
                if (!el.classList.contains('notranslate')) {
                    el.classList.add('notranslate');
                }
            });
        });
        iconObserver.observe(document.documentElement, { childList: true, subtree: true });
        document.querySelectorAll('.material-symbols-outlined').forEach(el => el.classList.add('notranslate'));

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

        // Theme Toggle Logic
        function toggleTheme() {
            const root = document.documentElement;
            const btnIcon = document.querySelector('#theme-toggle-btn span');
            
            if (root.getAttribute('data-theme') === 'light') {
                root.removeAttribute('data-theme');
                btnIcon.innerText = 'light_mode'; // Icon suggests switching to light mode
                localStorage.setItem('theme', 'dark');
            } else {
                root.setAttribute('data-theme', 'light');
                btnIcon.innerText = 'dark_mode'; // Icon suggests switching to dark mode
                localStorage.setItem('theme', 'light');
            }

            // Custom event so Map or other elements can redraw if necessary
            window.dispatchEvent(new Event('theme-changed'));
        }

        // Initialize Theme
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
            const btnIcon = document.querySelector('#theme-toggle-btn span');
            if(btnIcon) btnIcon.innerText = 'dark_mode';
        }

        // Google Translate Initialization Callback
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false
            }, 'google_translate_element');
        }

        // Programmatic Language Change via Cookie
        function changeLanguage(langCode) {
            if (langCode === 'en') {
                document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + window.location.hostname;
                localStorage.removeItem('selected_lang');
            } else {
                document.cookie = 'googtrans=/en/' + langCode + '; path=/;';
                document.cookie = 'googtrans=/en/' + langCode + '; path=/; domain=' + window.location.hostname;
                localStorage.setItem('selected_lang', langCode);
            }
            window.location.reload();
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>
