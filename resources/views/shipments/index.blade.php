@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="text-white fw-bold tracking-tight mb-0">Shipment Intelligence</h3>
        <div class="d-flex gap-2">
            <x-button variant="outline" icon="filter_list">Filter</x-button>
            <a href="{{ route('shipments.create') }}" class="text-decoration-none">
                <x-button variant="primary" icon="add">New Shipment</x-button>
            </a>
        </div>
    </div>

    <!-- Analytics Top Row -->
    <div class="row g-4 mb-2">
        <div class="col-md-3">
            <x-card title="Total Shipments" icon="local_shipping">
                <h2 class="text-white fw-bold mb-0 px-3 pb-3">{{ $stats['total'] }}</h2>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="Active Transit" icon="sailing" glow="cyan">
                <h2 class="text-white fw-bold mb-0 px-3 pb-3">{{ $stats['active'] }}</h2>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="Estimated Revenue" icon="payments" glow="success">
                <h2 class="text-success fw-bold mb-0 px-3 pb-3">${{ number_format($stats['revenue']) }}</h2>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="Risk Alerts" icon="warning" glow="danger">
                <h2 class="text-danger fw-bold mb-0 px-3 pb-3">0</h2>
            </x-card>
        </div>
    </div>

    <!-- Active Shipments Table -->
    <x-card title="Active & Recent Shipments" icon="table_chart">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                <thead style="background: rgba(255,255,255,0.05);">
                    <tr>
                        <th class="text-white fw-bold">Shipment #</th>
                        <th class="text-white fw-bold">Type</th>
                        <th class="text-white fw-bold">Commodity</th>
                        <th class="text-white fw-bold">Route</th>
                        <th class="text-white fw-bold">Status</th>
                        <th class="text-white fw-bold">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($shipments as $shipment)
                    <tr style="background: transparent; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
                        <td class="text-white fw-medium">{{ $shipment->shipment_number }}</td>
                        <td>
                            @if($shipment->type === 'import')
                                <x-badge variant="info">Import</x-badge>
                            @else
                                <x-badge variant="primary">Export</x-badge>
                            @endif
                        </td>
                        <td class="text-muted">{{ $shipment->commodity }}</td>
                        <td class="text-white">
                            {{ $shipment->origin_country }} <span class="material-symbols-outlined fs-7 text-muted align-middle mx-1">arrow_forward</span> {{ $shipment->destination_country }}
                        </td>
                        <td>
                            @if($shipment->status === 'Redirected')
                                <x-badge variant="purple">{{ $shipment->status }}</x-badge>
                            @elseif($shipment->status === 'Pending')
                                <x-badge variant="warning">{{ $shipment->status }}</x-badge>
                            @else
                                <x-badge variant="success">{{ $shipment->status }}</x-badge>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('shipments.show', $shipment) }}" class="text-decoration-none">
                                    <x-button variant="ghost" class="p-1">
                                        <span class="material-symbols-outlined fs-5">visibility</span>
                                    </x-button>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <span class="material-symbols-outlined fs-1 mb-2">inventory_2</span>
                            <p class="mb-0">No shipments found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</main>
@endsection
