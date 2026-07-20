<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control Center - GlobalChain</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        .animated-bg {
            background: radial-gradient(circle at 50% 50%, #2a0845, #11031d) !important;
        }
        .admin-sidebar {
            background: rgba(20, 5, 30, 0.8) !important;
            border-right: 1px solid rgba(255,0,100,0.2) !important;
        }
        .admin-nav {
            border-bottom: 1px solid rgba(255,0,100,0.2) !important;
        }
    </style>
</head>
<body>
    
    <!-- Animated Admin Background -->
    <div class="animated-bg"></div>

    <!-- Main Floating Layout -->
    <div class="app-layout">
        
        <!-- Top Navigation -->
        <nav class="top-nav glass-panel admin-nav">
            <div class="d-flex align-items-center">
                <span class="material-symbols-outlined text-danger me-2 fs-3">admin_panel_settings</span>
                <span class="fs-5 fw-bold text-white tracking-tight notranslate" style="letter-spacing: 0.5px;">GLOBAL<span class="text-danger" style="font-weight: 300;">ADMIN</span></span>
            </div>
            
            <div class="d-flex align-items-center gap-4 ms-auto">
                <div class="d-flex flex-column text-end d-none d-lg-flex">
                    <span class="fw-bold text-white lh-1" id="current-time">00:00:00 UTC</span>
                    <span class="fs-8 text-muted lh-1 mt-1" id="current-date">Jan 01, 2026</span>
                </div>

                <div class="dropdown border-start border-secondary ps-4">
                    <div class="d-flex align-items-center cursor-pointer dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-6 bg-danger" style="width: 36px; height: 36px; box-shadow: 0 0 15px rgba(255,0,100,0.5);">
                            {{ Auth::user() ? substr(Auth::user()->name, 0, 1) : 'A' }}
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow border-secondary mt-2 glass-panel">
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">Back to Main App</a></li>
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
            <aside class="left-sidebar glass-panel admin-sidebar">
                <div class="sidebar-logo-icon">
                    <span class="material-symbols-outlined fs-2 text-danger">shield</span>
                </div>
                
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">dashboard</span>
                            <span class="text">Dashboard</span>
                        </a>
                    </li>
                    <li class="mt-4 mb-2 px-4 text-muted fs-8 text-uppercase fw-bold">Kelola</li>
                    <li>
                        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">group</span>
                            <span class="text">User</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.shipments.index') }}" class="{{ request()->routeIs('admin.shipments.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">local_shipping</span>
                            <span class="text">Data Pelayaran</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ports.index') }}" class="{{ request()->routeIs('admin.ports.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">anchor</span>
                            <span class="text">Dataset Pelabuhan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.articles.index') }}" class="{{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">article</span>
                            <span class="text">Artikel Analisis</span>
                        </a>
                    </li>
                    <li class="mt-4 mb-2 px-4 text-muted fs-8 text-uppercase fw-bold">Sistem</li>
                    <li>
                        <a href="{{ route('admin.api-management') }}" class="{{ request()->routeIs('admin.api-management') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">api</span>
                            <span class="text">API Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">settings</span>
                            <span class="text">System Config</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.logs') }}" class="{{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                            <span class="material-symbols-outlined icon">history</span>
                            <span class="text">Audit Logs</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}" class="mt-5">
                            <span class="material-symbols-outlined icon text-muted">arrow_back</span>
                            <span class="text text-muted">Back to App</span>
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Dynamic Content -->
            @yield('content')
            
        </div>
    </div>

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
    </script>
</body>
</html>
