@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pointer-events-auto p-4">
    
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
                <!-- Map Placeholder -->
                <div class="w-100 rounded-3 mb-4 border border-secondary border-opacity-25 d-flex justify-content-center align-items-center" style="height: 300px; background: rgba(14, 165, 233, 0.05); overflow: hidden; position: relative;">
                    <div class="position-absolute d-flex flex-column align-items-center">
                        <span class="material-symbols-outlined text-cyan-glow fs-1 mb-2" style="color: var(--cyan-glow);">satellite_alt</span>
                        <span class="text-muted fw-bold">Interactive Route Map Rendering...</span>
                    </div>
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
