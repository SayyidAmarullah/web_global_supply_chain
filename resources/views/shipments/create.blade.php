@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pointer-events-auto p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="text-white fw-bold tracking-tight mb-0">Create New Shipment</h3>
        <a href="{{ route('shipments.index') }}" class="text-decoration-none">
            <x-button variant="outline" icon="arrow_back">Cancel</x-button>
        </a>
    </div>

    <x-card title="Shipment Details" icon="inventory_2" glow="cyan">
        <form action="{{ route('shipments.store') }}" method="POST" class="p-4">
            @csrf
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label text-muted fs-7 fw-medium text-uppercase">Trade Type</label>
                    <select name="type" class="form-select bg-transparent text-white border-secondary @error('type') is-invalid @enderror" required>
                        <option value="import" style="background: #041326;">Import</option>
                        <option value="export" style="background: #041326;">Export</option>
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted fs-7 fw-medium text-uppercase">Commodity</label>
                    <input type="text" name="commodity" class="form-control bg-transparent text-white border-secondary @error('commodity') is-invalid @enderror" required placeholder="e.g. Crude Oil, Wheat, Electronics" value="{{ old('commodity') }}">
                    @error('commodity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label text-muted fs-7 fw-medium text-uppercase">Quantity</label>
                    <input type="number" step="0.01" name="quantity" class="form-control bg-transparent text-white border-secondary @error('quantity') is-invalid @enderror" required value="{{ old('quantity') }}">
                    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fs-7 fw-medium text-uppercase">Unit</label>
                    <input type="text" name="unit" class="form-control bg-transparent text-white border-secondary @error('unit') is-invalid @enderror" required placeholder="e.g. Metric Tons, TEU" value="{{ old('unit') }}">
                    @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fs-7 fw-medium text-uppercase">Container Type</label>
                    <input type="text" name="container_type" class="form-control bg-transparent text-white border-secondary @error('container_type') is-invalid @enderror" required placeholder="e.g. Dry Van 40ft" value="{{ old('container_type') }}">
                    @error('container_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row g-4 mb-4 pt-3 border-top border-secondary border-opacity-25">
                <div class="col-md-6">
                    <h5 class="text-white fw-bold mb-3">Origin</h5>
                    <div class="mb-3">
                        <label class="form-label text-muted fs-7 fw-medium text-uppercase">Country</label>
                        <input type="text" name="origin_country" class="form-control bg-transparent text-white border-secondary @error('origin_country') is-invalid @enderror" required value="{{ old('origin_country') }}">
                    </div>
                    <div>
                        <label class="form-label text-muted fs-7 fw-medium text-uppercase">Port</label>
                        <input type="text" name="origin_port" class="form-control bg-transparent text-white border-secondary @error('origin_port') is-invalid @enderror" required value="{{ old('origin_port') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="text-white fw-bold mb-3">Destination</h5>
                    <div class="mb-3">
                        <label class="form-label text-muted fs-7 fw-medium text-uppercase">Country</label>
                        <input type="text" name="destination_country" class="form-control bg-transparent text-white border-secondary @error('destination_country') is-invalid @enderror" required value="{{ old('destination_country') }}">
                    </div>
                    <div>
                        <label class="form-label text-muted fs-7 fw-medium text-uppercase">Port</label>
                        <input type="text" name="destination_port" class="form-control bg-transparent text-white border-secondary @error('destination_port') is-invalid @enderror" required value="{{ old('destination_port') }}">
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4 pt-4 border-top border-secondary border-opacity-25">
                <x-button variant="primary" icon="check" type="submit">Deploy Shipment</x-button>
            </div>
        </form>
    </x-card>
</main>
@endsection
