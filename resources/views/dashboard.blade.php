@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pointer-events-auto p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="text-white fw-bold tracking-tight mb-0">Global Mission Control</h3>
        <x-button variant="primary" icon="refresh">Refresh Intelligence</x-button>
    </div>

    <!-- Row 1: Top Summaries -->
    <div class="row g-4">
        <!-- Global Statistics Placeholder -->
        <div class="col-md-3">
            <x-card title="Global Statistics" icon="public" glow="cyan">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fs-7">Monitored Countries</span>
                        <span class="text-white fw-bold">194</span>
                    </div>
                    <div class="w-100 bg-secondary bg-opacity-25 rounded-pill mb-3" style="height: 4px;">
                        <div class="bg-cyan-glow h-100 rounded-pill" style="width: 100%;"></div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Shipment Summary Placeholder -->
        <div class="col-md-3">
            <x-card title="Shipment Summary" icon="local_shipping">
                <div class="p-3">
                    <h3 class="text-white fw-bold mb-1">1,248</h3>
                    <x-badge variant="success" icon="check_circle">Active Transit</x-badge>
                </div>
            </x-card>
        </div>

        <!-- Risk Summary Placeholder -->
        <div class="col-md-3">
            <x-card title="Risk Summary" icon="warning" glow="danger">
                <div class="p-3">
                    <h3 class="text-white fw-bold mb-1">34</h3>
                    <x-badge variant="danger" icon="error">Critical Alerts</x-badge>
                </div>
            </x-card>
        </div>

        <!-- Trade Opportunity Summary Placeholder -->
        <div class="col-md-3">
            <x-card title="Trade Opportunity" icon="payments" glow="purple">
                <div class="p-3">
                    <h3 class="text-white fw-bold mb-1">94.5</h3>
                    <x-badge variant="purple" icon="star">High Potential</x-badge>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Row 2: Complex Summaries -->
    <div class="row g-4">
        <!-- AI Recommendation Panel Placeholder -->
        <div class="col-md-8">
            <x-card title="AI Decision Support Engine" icon="smart_toy" glow="purple">
                <div class="p-4">
                    <x-alert variant="warning" icon="warning">
                        <strong>Redirection Suggested:</strong> Consider redirecting 3 shipments from Suez Canal to Cape of Good Hope due to escalating regional tensions. Estimated savings: $45,000.
                    </x-alert>
                    <div class="mt-3">
                        <x-button variant="outline" class="me-2">Dismiss</x-button>
                        <x-button variant="primary">Analyze Reroute</x-button>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Weather & Currency Summary Placeholder -->
        <div class="col-md-4 d-flex flex-column gap-4">
            <x-card title="Weather Intelligence" icon="storm">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">North Atlantic</span>
                        <x-badge variant="danger">Category 4</x-badge>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">South China Sea</span>
                        <x-badge variant="warning">Heavy Rain</x-badge>
                    </div>
                </div>
            </x-card>

            <x-card title="Currency Volatility" icon="currency_exchange">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">USD/EUR</span>
                        <span class="text-success fw-bold">+0.45%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">USD/JPY</span>
                        <span class="text-danger fw-bold">-1.20%</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Row 3: More Intel -->
    <div class="row g-4 mb-4">
        <!-- Commodity Summary Placeholder -->
        <div class="col-md-4">
            <x-card title="Commodity Market" icon="inventory_2">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-white">Crude Oil</span>
                        <span class="text-danger fw-bold">$82.40 ↓</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-white">Gold</span>
                        <span class="text-success fw-bold">$2,410 ↑</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-white">Wheat</span>
                        <span class="text-success fw-bold">$5.60 ↑</span>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Country Summary Placeholder -->
        <div class="col-md-4">
            <x-card title="Country Intel" icon="language">
                <div class="p-3">
                    <p class="text-muted fs-7">Top Trade Destinations</p>
                    <ol class="text-white ps-3 mb-0">
                        <li class="mb-2">Germany (Opportunity: 94)</li>
                        <li class="mb-2">Singapore (Opportunity: 88)</li>
                        <li>Brazil (Opportunity: 82)</li>
                    </ol>
                </div>
            </x-card>
        </div>

        <!-- Latest News Placeholder -->
        <div class="col-md-4">
            <x-card title="Global News" icon="newspaper">
                <div class="p-3">
                    <p class="text-white mb-2 fs-7">Port strike expected to delay shipments in US East Coast...</p>
                    <x-badge variant="danger" class="mb-3">High Impact</x-badge>
                    
                    <p class="text-white mb-2 fs-7">New trade agreement signed lowering tariffs on agricultural goods...</p>
                    <x-badge variant="success">Positive Sentiment</x-badge>
                </div>
            </x-card>
        </div>
    </div>

</main>
@endsection
