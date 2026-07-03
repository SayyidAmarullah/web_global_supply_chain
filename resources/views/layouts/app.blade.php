<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Global Supply Chain Risk Intelligence Platform') }} - @yield('title', 'Dashboard')</title>

    <!-- Google Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar d-flex flex-column p-3" style="width: 260px;">
            <div class="d-flex align-items-center mb-4 px-3">
                <span class="material-symbols-outlined text-primary me-2 fs-2">public</span>
                <span class="fs-5 fw-bold text-secondary">GLOBAL<span class="text-primary">CHAIN</span></span>
            </div>
            
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }} d-flex align-items-center">
                        <span class="material-symbols-outlined me-3">dashboard</span>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link d-flex align-items-center">
                        <span class="material-symbols-outlined me-3">directions_boat</span>
                        Shipments
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link d-flex align-items-center">
                        <span class="material-symbols-outlined me-3">map</span>
                        World Map
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link d-flex align-items-center">
                        <span class="material-symbols-outlined me-3">flag</span>
                        Countries
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link d-flex align-items-center">
                        <span class="material-symbols-outlined me-3">anchor</span>
                        Ports
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link d-flex align-items-center">
                        <span class="material-symbols-outlined me-3">partly_cloudy_day</span>
                        Weather
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link d-flex align-items-center">
                        <span class="material-symbols-outlined me-3">currency_exchange</span>
                        Currency
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link d-flex align-items-center text-danger">
                        <span class="material-symbols-outlined me-3">warning</span>
                        Risk Intelligence
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link d-flex align-items-center">
                        <span class="material-symbols-outlined me-3">analytics</span>
                        Analytics
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link d-flex align-items-center">
                        <span class="material-symbols-outlined me-3">newspaper</span>
                        News
                    </a>
                </li>
            </ul>
            
            <hr>
            
            <div class="dropdown px-3">
                <a href="#" class="d-flex align-items-center text-secondary text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0B5ED7&color=fff" alt="" width="32" height="32" class="rounded-circle me-2">
                    <strong>{{ Auth::user()->name }}</strong>
                </a>
                <ul class="dropdown-menu shadow">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">Sign out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="flex-grow-1 bg-light">
            <!-- Topbar -->
            <header class="topbar py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center w-50">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                            <span class="material-symbols-outlined text-muted fs-5">search</span>
                        </span>
                        <input type="text" class="form-control border-start-0 rounded-end-pill" placeholder="Search shipments, ports, or countries...">
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="text-muted d-none d-md-block fs-7" id="current-time"></div>
                    <button class="btn btn-light rounded-circle p-2 d-flex align-items-center position-relative">
                        <span class="material-symbols-outlined text-secondary fs-5">notifications</span>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                            <span class="visually-hidden">New alerts</span>
                        </span>
                    </button>
                    <button class="btn btn-light rounded-circle p-2 d-flex align-items-center">
                        <span class="material-symbols-outlined text-secondary fs-5">dark_mode</span>
                    </button>
                    <button class="btn btn-light rounded-circle p-2 d-flex align-items-center">
                        <span class="material-symbols-outlined text-secondary fs-5">translate</span>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4" style="min-height: calc(100vh - 72px); overflow-y: auto; max-height: calc(100vh - 72px);">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Update current time
        setInterval(() => {
            document.getElementById('current-time').innerText = new Date().toUTCString();
        }, 1000);
    </script>
    
    @stack('scripts')
</body>
</html>
