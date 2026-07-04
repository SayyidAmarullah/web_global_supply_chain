<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Global Supply Chain') }} - @yield('title', 'Command Center')</title>

    <!-- Google Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-container">
        
        <!-- Main Workspace (Moved to Left) -->
        <main class="main-wrapper">
            <!-- Floating Topbar -->
            <header class="floating-topbar">
                <div class="search-pill">
                    <span class="material-symbols-outlined text-muted fs-5">search</span>
                    <input type="text" placeholder="Track shipment, port, or vessel...">
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex flex-column text-end me-2 d-none d-md-flex">
                        <span class="fw-bold text-secondary lh-1" id="current-time">00:00:00</span>
                        <span class="fs-7 text-muted lh-1 mt-1">UTC Time</span>
                    </div>
                    
                    <button class="btn btn-white rounded-circle p-2 d-flex align-items-center position-relative shadow-sm border border-light">
                        <span class="material-symbols-outlined text-secondary fs-5">notifications</span>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    </button>
                    
                    <button class="btn btn-primary rounded-circle p-2 d-flex align-items-center shadow-sm">
                        <span class="material-symbols-outlined text-white fs-5">add</span>
                    </button>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="main-content">
                @yield('content')
            </div>
        </main>
        
        <!-- Floating Sidebar (Moved to Right) -->
        <nav class="floating-sidebar">
            <div class="sidebar-logo text-end">
                <div class="d-flex align-items-center justify-content-end mb-1">
                    <span class="fs-4 fw-bolder text-white tracking-tight">GLOBAL<span class="text-accent">CHAIN</span></span>
                    <span class="material-symbols-outlined text-accent ms-2 fs-2">public</span>
                </div>
                <div class="text-white-50 fs-7 text-uppercase tracking-widest pe-1">Command Center</div>
            </div>
            
            <ul class="nav flex-column nav-custom mt-3">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="material-symbols-outlined me-3">space_dashboard</span>
                        Command Center
                    </a>
                </li>
                <li class="nav-item mt-4 mb-2 ms-4 text-white-50 fs-7 text-uppercase fw-semibold">Operations</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="material-symbols-outlined me-3">directions_boat</span>
                        Live Shipments
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="material-symbols-outlined me-3">explore</span>
                        Global Map
                    </a>
                </li>
                
                <li class="nav-item mt-4 mb-2 ms-4 text-white-50 fs-7 text-uppercase fw-semibold">Intelligence</li>
                <li class="nav-item">
                    <a href="#" class="nav-link text-warning">
                        <span class="material-symbols-outlined me-3">warning</span>
                        Risk Monitoring
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="material-symbols-outlined me-3">thunderstorm</span>
                        Weather Alerts
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="material-symbols-outlined me-3">trending_up</span>
                        Economic Data
                    </a>
                </li>
            </ul>
            
            <div class="user-badge mt-auto cursor-pointer dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="bg-white text-secondary rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 36px; height: 36px;">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="d-flex flex-column">
                    <span class="fw-bold fs-6 lh-1 mb-1">{{ Auth::user()->name }}</span>
                    <span class="fs-7 text-white-50 text-capitalize lh-1">{{ Auth::user()->role }}</span>
                </div>
                <span class="material-symbols-outlined ms-auto fs-5 text-white-50">more_vert</span>
            </div>
            
            <ul class="dropdown-menu dropdown-menu-dark shadow-lg border-0 mb-2">
                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}"><span class="material-symbols-outlined me-2 fs-5">person</span> Profile</a></li>
                <li><a class="dropdown-item d-flex align-items-center" href="#"><span class="material-symbols-outlined me-2 fs-5">settings</span> Settings</a></li>
                <li><hr class="dropdown-divider border-secondary"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center"><span class="material-symbols-outlined me-2 fs-5">logout</span> Sign Out</button>
                    </form>
                </li>
            </ul>
        </nav>
        
    </div>

    <script>
        // Real-time clock update
        function updateClock() {
            const now = new Date();
            document.getElementById('current-time').innerText = 
                now.getUTCHours().toString().padStart(2, '0') + ':' + 
                now.getUTCMinutes().toString().padStart(2, '0') + ':' + 
                now.getUTCSeconds().toString().padStart(2, '0');
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    
    @stack('scripts')
</body>
</html>
