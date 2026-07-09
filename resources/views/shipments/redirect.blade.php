@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pointer-events-auto p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h3 class="text-white fw-bold tracking-tight mb-1">Smart Shipment Redirection</h3>
            <span class="text-muted fs-7">Rerouting {{ $shipment->shipment_number }} ({{ $shipment->commodity }})</span>
        </div>
        <a href="{{ route('shipments.show', $shipment) }}" class="text-decoration-none">
            <x-button variant="outline" icon="arrow_back">Cancel</x-button>
        </a>
    </div>

    <div class="row g-4">
        <!-- Current Route Info -->
        <div class="col-md-4">
            <x-card title="Current Destination" icon="location_on" glow="danger">
                <div class="p-3">
                    <h4 class="text-white fw-bold mb-0">{{ $shipment->destination_country }}</h4>
                    <p class="text-muted fs-7 mb-3">{{ $shipment->destination_port }}</p>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fs-7">Estimated Profit</span>
                        <span class="text-white fw-bold">${{ number_format($shipment->estimated_profit ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fs-7">Shipping Cost</span>
                        <span class="text-danger fw-bold">-${{ number_format($shipment->shipping_cost ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fs-7">ETA</span>
                        <span class="text-white fw-bold">{{ $shipment->estimated_arrival ? $shipment->estimated_arrival->format('M d, Y') : 'Unknown' }}</span>
                    </div>
                </div>
            </x-card>

            <!-- AI Recommendation (Mockup) -->
            <x-card title="AI Recommendation" icon="smart_toy" glow="purple" class="mt-4">
                <div class="p-3">
                    <x-badge variant="purple" class="mb-2">Highest Profit Margin</x-badge>
                    <h5 class="text-white fw-bold">Germany, Port of Hamburg</h5>
                    <p class="text-muted fs-7">Current market price for {{ $shipment->commodity }} in Germany is 12% higher. Rerouting will increase net profit despite higher shipping cost.</p>
                </div>
            </x-card>
        </div>

        <!-- Redirection Form -->
        <div class="col-md-8">
            <x-card title="Configure New Destination" icon="alt_route" glow="cyan">
                <form action="{{ route('shipments.storeRedirect', $shipment) }}" method="POST" class="p-4">
                    @csrf
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted fs-7 fw-medium text-uppercase">New Destination Country</label>
                            <input type="text" name="destination_country" class="form-control bg-transparent text-white border-secondary @error('destination_country') is-invalid @enderror" required value="{{ old('destination_country') }}" placeholder="e.g. Germany">
                            @error('destination_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fs-7 fw-medium text-uppercase">New Destination Port</label>
                            <input type="text" name="destination_port" class="form-control bg-transparent text-white border-secondary @error('destination_port') is-invalid @enderror" required value="{{ old('destination_port') }}" placeholder="e.g. Port of Hamburg">
                            @error('destination_port')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted fs-7 fw-medium text-uppercase">New ETA</label>
                            <input type="date" name="estimated_arrival" class="form-control bg-transparent text-white border-secondary @error('estimated_arrival') is-invalid @enderror" required value="{{ old('estimated_arrival') }}">
                            @error('estimated_arrival')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted fs-7 fw-medium text-uppercase">New Shipping Cost</label>
                            <input type="number" step="0.01" name="shipping_cost" class="form-control bg-transparent text-white border-secondary @error('shipping_cost') is-invalid @enderror" required value="{{ old('shipping_cost') }}">
                            @error('shipping_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted fs-7 fw-medium text-uppercase">New Estimated Profit</label>
                            <input type="number" step="0.01" name="estimated_profit" class="form-control bg-transparent text-white border-secondary @error('estimated_profit') is-invalid @enderror" required value="{{ old('estimated_profit') }}">
                            @error('estimated_profit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted fs-7 fw-medium text-uppercase">Reason for Redirection</label>
                        <textarea name="reason" rows="3" class="form-control bg-transparent text-white border-secondary @error('reason') is-invalid @enderror" required placeholder="Provide justification for rerouting...">{{ old('reason') }}</textarea>
                        @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4 pt-4 border-top border-secondary border-opacity-25">
                        <x-button variant="primary" class="bg-purple-neon border-purple hover-neon-text" style="background-color: var(--purple-neon); color: white;" icon="alt_route" type="submit">Execute Redirection</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</main>
@endsection
