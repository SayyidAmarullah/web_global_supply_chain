@extends('layouts.app')
@section('title', 'Shipment Operations')

@section('content')
<main class="content-area d-flex flex-column flex-grow-1 p-4 h-100 pointer-events-auto" style="overflow-y: hidden;">
    
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-shrink-0">
        <div>
            <h3 class="fw-bold mb-1 text-white">Live Shipments</h3>
            <p class="text-muted fs-7 mb-0">Monitor and manage all active global transit operations.</p>
        </div>
        <div class="d-flex gap-3">
            <button class="btn btn-outline-info rounded-pill px-4 py-2 d-flex align-items-center" style="border-color: var(--cyan-glow); color: var(--cyan-glow);">
                <span class="material-symbols-outlined fs-6 me-2">download</span>
                <span class="fs-7 fw-bold">Export PDF</span>
            </button>
            <button class="btn btn-outline-primary rounded-pill px-4 py-2 d-flex align-items-center" style="border-color: var(--electric-blue); color: var(--electric-blue);">
                <span class="material-symbols-outlined fs-6 me-2">table_view</span>
                <span class="fs-7 fw-bold">Export Excel</span>
            </button>
        </div>
    </div>

    <!-- Main Table Container (Glassmorphism) -->
    <div class="glass-panel w-100 d-flex flex-column flex-grow-1 overflow-hidden" style="border-radius: 24px;">
        
        <!-- Toolbar -->
        <div class="p-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center flex-shrink-0">
            <div class="search-global m-0" style="width: 400px; background: rgba(0,0,0,0.3);">
                <span class="material-symbols-outlined text-muted fs-5">search</span>
                <input type="text" placeholder="Search by code, vessel, container...">
            </div>
            
            <div class="d-flex gap-3">
                <button class="btn btn-link text-muted text-decoration-none d-flex align-items-center px-3 rounded-pill" style="border: 1px solid rgba(255,255,255,0.1);">
                    <span class="material-symbols-outlined fs-6 me-2">tune</span>
                    <span class="fs-7 fw-bold">Advanced Filters</span>
                </button>
                <button class="btn btn-link text-muted text-decoration-none d-flex align-items-center px-3 rounded-pill" style="border: 1px solid rgba(255,255,255,0.1);">
                    <span class="material-symbols-outlined fs-6 me-2">sort</span>
                    <span class="fs-7 fw-bold">Sort</span>
                </button>
            </div>
        </div>

        <!-- Table Responsive -->
        <div class="table-responsive flex-grow-1">
            <table class="table table-borderless table-hover mb-0 align-middle w-100 h-100" style="color: var(--text-main);">
                <thead class="text-uppercase fs-8 sticky-top" style="background: rgba(10, 17, 40, 0.95); backdrop-filter: blur(24px);">
                    <tr>
                        <th class="ps-4 fw-bold py-4 border-bottom border-secondary border-opacity-25" style="background: transparent; color: #A0ABC0 !important;">Shipment Code</th>
                        <th class="fw-bold py-4 border-bottom border-secondary border-opacity-25" style="background: transparent; color: #A0ABC0 !important;">Vessel / Container</th>
                        <th class="fw-bold py-4 border-bottom border-secondary border-opacity-25" style="background: transparent; color: #A0ABC0 !important;">Route</th>
                        <th class="fw-bold py-4 border-bottom border-secondary border-opacity-25" style="background: transparent; color: #A0ABC0 !important;">Location</th>
                        <th class="fw-bold py-4 border-bottom border-secondary border-opacity-25" style="background: transparent; color: #A0ABC0 !important;">Status</th>
                        <th class="fw-bold py-4 border-bottom border-secondary border-opacity-25" style="background: transparent; color: #A0ABC0 !important;">Risk</th>
                        <th class="fw-bold py-4 border-bottom border-secondary border-opacity-25" style="background: transparent; color: #A0ABC0 !important;">ETA</th>
                        <th class="text-end pe-4 fw-bold py-4 border-bottom border-secondary border-opacity-25" style="background: transparent; color: #A0ABC0 !important;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <!-- Row 1 -->
                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: background 0.3s;">
                        <td class="ps-4 py-4" style="background: transparent;">
                            <span class="fw-bold text-white fs-7">#SHP-9021</span><br>
                            <span class="fs-8" style="color: var(--cyan-glow);">Ocean Freight</span>
                        </td>
                        <td class="py-4" style="background: transparent;">
                            <span class="fw-bold text-white d-block fs-7">MSC Isabella</span>
                            <span class="fs-8 text-muted font-monospace">MSCU-7738201</span>
                        </td>
                        <td class="py-4" style="background: transparent;">
                            <div class="d-flex align-items-center">
                                <span class="fs-8 fw-bold text-white">Shanghai</span>
                                <span class="material-symbols-outlined mx-2 fs-7 text-muted">arrow_forward</span>
                                <span class="fs-8 fw-bold text-white">Rotterdam</span>
                            </div>
                        </td>
                        <td class="py-4 fs-8 text-muted" style="background: transparent;">Suez Canal</td>
                        <td class="py-4" style="background: transparent;">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-semibold fs-8 d-inline-flex align-items-center">
                                <span class="material-symbols-outlined fs-7 me-1">sailing</span> In Transit
                            </span>
                        </td>
                        <td class="py-4" style="background: transparent;">
                            <span class="d-flex align-items-center text-success fs-8 fw-bold">
                                <span class="material-symbols-outlined fs-6 me-1">verified_user</span> Low
                            </span>
                        </td>
                        <td class="py-4 fs-8 fw-bold text-white" style="background: transparent;">Jul 12, 2026</td>
                        <td class="text-end pe-4 py-4" style="background: transparent;">
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0 text-decoration-none" data-bs-toggle="dropdown">
                                    <span class="material-symbols-outlined">more_horiz</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark shadow-lg border-secondary border-opacity-25 fs-8 glass-panel">
                                    <li><a class="dropdown-item d-flex align-items-center py-2" href="#"><span class="material-symbols-outlined me-2 fs-6">visibility</span> View</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center py-2" style="color: var(--cyan-glow);" href="#"><span class="material-symbols-outlined me-2 fs-6">my_location</span> Track</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center py-2 text-warning" href="#"><span class="material-symbols-outlined me-2 fs-6">alt_route</span> Redirect</a></li>
                                    <li><hr class="dropdown-divider border-secondary border-opacity-25"></li>
                                    <li><a class="dropdown-item d-flex align-items-center py-2 text-danger" href="#"><span class="material-symbols-outlined me-2 fs-6">delete</span> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Row 2 -->
                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: background 0.3s;">
                        <td class="ps-4 py-4" style="background: transparent;">
                            <span class="fw-bold text-white fs-7">#SHP-8105</span><br>
                            <span class="fs-8" style="color: var(--purple-neon);">Air Freight</span>
                        </td>
                        <td class="py-4" style="background: transparent;">
                            <span class="fw-bold text-white d-block fs-7">Boeing 777F</span>
                            <span class="fs-8 text-muted font-monospace">AWB-0209931</span>
                        </td>
                        <td class="py-4" style="background: transparent;">
                            <div class="d-flex align-items-center">
                                <span class="fs-8 fw-bold text-white">Frankfurt</span>
                                <span class="material-symbols-outlined mx-2 fs-7 text-muted">arrow_forward</span>
                                <span class="fs-8 fw-bold text-white">New York</span>
                            </div>
                        </td>
                        <td class="py-4 fs-8 text-muted" style="background: transparent;">North Atlantic</td>
                        <td class="py-4" style="background: transparent;">
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-2 fw-semibold fs-8 d-inline-flex align-items-center">
                                <span class="material-symbols-outlined fs-7 me-1">schedule</span> Delayed
                            </span>
                        </td>
                        <td class="py-4" style="background: transparent;">
                            <span class="d-flex align-items-center text-warning fs-8 fw-bold">
                                <span class="material-symbols-outlined fs-6 me-1">warning</span> Medium
                            </span>
                        </td>
                        <td class="py-4 fs-8 fw-bold text-white" style="background: transparent;">Jul 05, 2026</td>
                        <td class="text-end pe-4 py-4" style="background: transparent;">
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0 text-decoration-none" data-bs-toggle="dropdown">
                                    <span class="material-symbols-outlined">more_horiz</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark shadow-lg border-secondary border-opacity-25 fs-8 glass-panel">
                                    <li><a class="dropdown-item d-flex align-items-center py-2" href="#"><span class="material-symbols-outlined me-2 fs-6">visibility</span> View</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center py-2" style="color: var(--cyan-glow);" href="#"><span class="material-symbols-outlined me-2 fs-6">my_location</span> Track</a></li>
                                    <li><hr class="dropdown-divider border-secondary border-opacity-25"></li>
                                    <li><a class="dropdown-item d-flex align-items-center py-2 text-danger" href="#"><span class="material-symbols-outlined me-2 fs-6">delete</span> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Row 3 -->
                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: background 0.3s;">
                        <td class="ps-4 py-4" style="background: transparent;">
                            <span class="fw-bold text-white fs-7">#SHP-7742</span><br>
                            <span class="fs-8" style="color: var(--cyan-glow);">Ocean Freight</span>
                        </td>
                        <td class="py-4" style="background: transparent;">
                            <span class="fw-bold text-white d-block fs-7">Ever Given</span>
                            <span class="fs-8 text-muted font-monospace">EGLV-110294</span>
                        </td>
                        <td class="py-4" style="background: transparent;">
                            <div class="d-flex align-items-center">
                                <span class="fs-8 fw-bold text-white">Shenzhen</span>
                                <span class="material-symbols-outlined mx-2 fs-7 text-muted">arrow_forward</span>
                                <span class="fs-8 fw-bold text-white">Los Angeles</span>
                            </div>
                        </td>
                        <td class="py-4 fs-8 text-muted" style="background: transparent;">Pacific Ocean</td>
                        <td class="py-4" style="background: transparent;">
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2 fw-semibold fs-8 d-inline-flex align-items-center">
                                <span class="material-symbols-outlined fs-7 me-1">error</span> Rerouted
                            </span>
                        </td>
                        <td class="py-4" style="background: transparent;">
                            <span class="d-flex align-items-center text-danger fs-8 fw-bold">
                                <span class="material-symbols-outlined fs-6 me-1">gpp_bad</span> High
                            </span>
                        </td>
                        <td class="py-4 fs-8 fw-bold text-white" style="background: transparent;">Jul 20, 2026</td>
                        <td class="text-end pe-4 py-4" style="background: transparent;">
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0 text-decoration-none" data-bs-toggle="dropdown">
                                    <span class="material-symbols-outlined">more_horiz</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark shadow-lg border-secondary border-opacity-25 fs-8 glass-panel">
                                    <li><a class="dropdown-item d-flex align-items-center py-2" href="#"><span class="material-symbols-outlined me-2 fs-6">visibility</span> View</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center py-2" style="color: var(--cyan-glow);" href="#"><span class="material-symbols-outlined me-2 fs-6">my_location</span> Track</a></li>
                                    <li><hr class="dropdown-divider border-secondary border-opacity-25"></li>
                                    <li><a class="dropdown-item d-flex align-items-center py-2 text-danger" href="#"><span class="material-symbols-outlined me-2 fs-6">delete</span> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-4 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center flex-shrink-0" style="background: rgba(0,0,0,0.2);">
            <span class="fs-8 text-muted">Showing 1 to 3 of 152 shipments</span>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary border-0 text-muted p-1"><span class="material-symbols-outlined fs-6">chevron_left</span></button>
                <button class="btn btn-sm btn-primary rounded px-3 py-1 fs-8 fw-bold" style="background: var(--electric-blue); border-color: var(--electric-blue);">1</button>
                <button class="btn btn-sm btn-outline-secondary border-0 rounded px-3 py-1 fs-8 text-muted">2</button>
                <button class="btn btn-sm btn-outline-secondary border-0 rounded px-3 py-1 fs-8 text-muted">3</button>
                <button class="btn btn-sm btn-outline-secondary border-0 text-muted p-1"><span class="material-symbols-outlined fs-6">chevron_right</span></button>
            </div>
        </div>
    </div>
</main>
@endsection
