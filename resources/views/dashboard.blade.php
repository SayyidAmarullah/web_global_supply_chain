@extends('layouts.app')

@section('content')
<!-- Dashboard Content Area (Flex container holding left widgets area & right panel) -->
<main class="content-area d-flex w-100 h-100 gap-3" style="pointer-events: none;">
    
    <!-- Center Column (Widgets & Empty space for map) -->
    <div class="d-flex flex-column flex-grow-1 h-100">
        <!-- Floating Widgets Overlay -->
        <div class="floating-widgets pointer-events-auto w-100">
            
            <!-- Widget 1 -->
            <div class="widget-card glass-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="metric-icon-box">
                            <span class="material-symbols-outlined text-cyan-glow" style="color: var(--cyan-glow);">sailing</span>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 fs-7">Active Shipments</h6>
                            <h3 class="text-white fw-bold mb-0">1,248</h3>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 rounded-pill px-3 py-1">
                            +12% <span class="material-symbols-outlined fs-7 align-middle">trending_up</span>
                        </span>
                    </div>
                </div>
                <div class="w-100 bg-secondary bg-opacity-25 rounded-pill" style="height: 4px;">
                    <div class="bg-cyan-glow h-100 rounded-pill" style="width: 75%; background-color: var(--cyan-glow); box-shadow: 0 0 10px var(--cyan-glow);"></div>
                </div>
            </div>

            <!-- Widget 2 -->
            <div class="widget-card glass-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="metric-icon-box">
                            <span class="material-symbols-outlined text-warning" style="color: var(--warning);">warning</span>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 fs-7">Global Risk Alerts</h6>
                            <h3 class="text-white fw-bold mb-0">34</h3>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50 rounded-pill px-3 py-1">
                            High Risk
                        </span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="text-muted fs-8"><strong class="text-white">12</strong> Weather</span>
                    <span class="text-muted fs-8">•</span>
                    <span class="text-muted fs-8"><strong class="text-white">22</strong> Political</span>
                </div>
            </div>

            <!-- Widget 3 -->
            <div class="widget-card glass-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="metric-icon-box">
                            <span class="material-symbols-outlined text-purple-neon" style="color: var(--purple-neon);">payments</span>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0 fs-7">Opportunity Score</h6>
                            <h3 class="text-white fw-bold mb-0">94.5</h3>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="text-success fs-7 fw-bold">Germany</span>
                    </div>
                </div>
                <p class="text-muted fs-8 mb-0">Best export destination for <strong>Wheat</strong> today.</p>
            </div>

        </div>
        <div class="flex-grow-1"></div> <!-- Empty space for map to show through -->
    </div>

    <!-- Right Information Panel (Flex item, not absolute) -->
    <aside class="right-info-panel glass-panel flex-shrink-0 pointer-events-auto h-100 position-relative" style="width: 360px;">
        <div class="px-4 pb-3 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center flex-shrink-0">
            <h5 class="text-white fw-bold mb-0">System Status</h5>
            <span class="material-symbols-outlined text-muted cursor-pointer hover-neon-text">close</span>
        </div>
        
        <div class="p-4 flex-grow-1 overflow-auto">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="spinner-grow spinner-grow-sm text-success" role="status"></div>
                <span class="text-muted">AI Analysis Engine Online</span>
            </div>
            
            <div class="glass-pill p-3 mb-4">
                <h6 class="text-cyan-glow fw-bold mb-2" style="color: var(--cyan-glow);">Recommendation</h6>
                <p class="text-muted fs-8 mb-0">Consider redirecting 3 shipments from Suez Canal to Cape of Good Hope due to escalating regional tensions. Estimated savings: $45,000.</p>
                <button class="btn btn-sm btn-outline-info w-100 mt-3 rounded-pill" style="border-color: var(--cyan-glow); color: var(--cyan-glow);">Analyze Reroute</button>
            </div>
            
            <h6 class="text-white fw-bold mb-3">Top Commodities</h6>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fs-7">Crude Oil</span>
                <span class="text-danger fw-bold fs-7">$82.40 <span class="material-symbols-outlined fs-8 align-middle">arrow_downward</span></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fs-7">Gold</span>
                <span class="text-success fw-bold fs-7">$2,410 <span class="material-symbols-outlined fs-8 align-middle">arrow_upward</span></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fs-7">Wheat</span>
                <span class="text-success fw-bold fs-7">$5.60 <span class="material-symbols-outlined fs-8 align-middle">arrow_upward</span></span>
            </div>
        </div>
    </aside>
</main>
@endsection
