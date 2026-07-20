@extends('layouts.admin')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <a href="{{ route('admin.index') }}" class="text-muted text-decoration-none hover-white"><span class="material-symbols-outlined fs-4">arrow_back</span></a>
                <span class="material-symbols-outlined text-danger fs-2">local_shipping</span> 
                Shipment Data Management
            </h1>
            <p class="text-muted fs-7 mt-1 ms-5">Monitor and manage all maritime shipments</p>
        </div>
    </div>

    <!-- Main Container -->
    <div class="flex-grow-1">
        <x-card title="Registered Shipments" icon="table_chart" glow="red" class="h-100">
            <!-- Filter Bar -->
            <div class="px-3 pt-3 pb-2 border-bottom border-secondary border-opacity-25">
                <form action="{{ route('admin.shipments.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-muted"><span class="material-symbols-outlined fs-6">search</span></span>
                            <input type="text" name="search" class="form-control border-secondary text-white shadow-none" style="background-color: #121826;" placeholder="Search shipment #, commodity or ports..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-muted"><span class="material-symbols-outlined fs-6">sailing</span></span>
                            <select name="status" class="form-select border-secondary text-white shadow-none" style="background-color: #121826;">
                                <option value="">All Statuses</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="Redirected" {{ request('status') == 'Redirected' ? 'selected' : '' }}>Redirected</option>
                                <option value="Delayed" {{ request('status') == 'Delayed' ? 'selected' : '' }}>Delayed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 text-end d-flex gap-2">
                        <button type="submit" class="btn btn-outline-danger w-100">Filter</button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.shipments.index') }}" class="btn btn-outline-secondary px-3" title="Clear Filters"><span class="material-symbols-outlined fs-6 align-middle">close</span></a>
                        @endif
                    </div>
                </form>
            </div>
            
            <div class="table-responsive p-3 h-100 overflow-auto" style="max-height: calc(100vh - 270px);">
                @if(session('success'))
                    <div class="alert alert-success bg-success bg-opacity-10 border-success border-opacity-25 text-success mb-3">
                        {{ session('success') }}
                    </div>
                @endif
                
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr class="border-bottom border-secondary border-opacity-25">
                            <th class="text-muted fs-8 text-uppercase pb-2">Shipment #</th>
                            <th class="text-muted fs-8 text-uppercase pb-2">Type</th>
                            <th class="text-muted fs-8 text-uppercase pb-2">Commodity</th>
                            <th class="text-muted fs-8 text-uppercase pb-2">Route</th>
                            <th class="text-muted fs-8 text-uppercase pb-2">Status</th>
                            <th class="text-muted fs-8 text-uppercase pb-2 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                        <tr>
                            <td class="text-white fw-bold">{{ $shipment->shipment_number }}</td>
                            <td>
                                @if($shipment->type === 'import')
                                    <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25">Import</span>
                                @else
                                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25">Export</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $shipment->commodity }}</td>
                            <td class="text-white">
                                {{ $shipment->origin_country }} <span class="material-symbols-outlined fs-7 text-muted align-middle mx-1">arrow_forward</span> {{ $shipment->destination_country }}
                            </td>
                            <td>
                                @if($shipment->status === 'Redirected')
                                    <span class="badge bg-purple bg-opacity-25 text-purple border border-purple border-opacity-25" style="color: var(--purple-neon) !important; border-color: var(--purple-neon) !important;">{{ $shipment->status }}</span>
                                @elseif($shipment->status === 'Pending')
                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25">{{ $shipment->status }}</span>
                                @else
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25">{{ $shipment->status }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('shipments.show', $shipment->id) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Track & Trace" target="_blank">
                                        <span class="material-symbols-outlined fs-6 align-middle">visibility</span>
                                    </a>
                                    <form action="{{ route('admin.shipments.delete', $shipment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this shipment? This action can be undone via database restore if soft-deletes are active.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete">
                                            <span class="material-symbols-outlined fs-6 align-middle">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <span class="material-symbols-outlined fs-1 mb-2">inventory_2</span>
                                <p class="mb-0">No shipments found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4 d-flex justify-content-center pagination-dark">
                    {{ $shipments->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </x-card>
    </div>
</main>

<style>
    .pagination-dark .pagination {
        margin-bottom: 0;
        --bs-pagination-bg: transparent;
        --bs-pagination-border-color: rgba(255,255,255,0.1);
        --bs-pagination-color: #a0aec0;
        --bs-pagination-hover-bg: rgba(255,255,255,0.05);
        --bs-pagination-hover-color: #fff;
        --bs-pagination-hover-border-color: rgba(255,255,255,0.2);
        --bs-pagination-focus-bg: rgba(255,255,255,0.05);
        --bs-pagination-active-bg: rgba(220, 53, 69, 0.2);
        --bs-pagination-active-border-color: rgba(220, 53, 69, 0.4);
        --bs-pagination-active-color: #fff;
    }
</style>
@endsection
