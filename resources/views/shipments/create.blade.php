@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="text-white fw-bold tracking-tight mb-0">Create New Shipment</h3>
        <a href="{{ route('shipments.index') }}" class="text-decoration-none">
            <x-button variant="outline" icon="arrow_back">Cancel</x-button>
        </a>
    </div>

    <x-card title="Shipment Details" icon="inventory_2" glow="cyan">
        <form action="{{ route('shipments.store') }}" method="POST" class="p-4" id="createShipmentForm">
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
                    <select name="commodity" class="form-select bg-transparent text-white border-secondary @error('commodity') is-invalid @enderror" required>
                        <option value="" disabled selected style="background: #041326;">Select Commodity</option>
                        @foreach($commodities as $commodity)
                            <option value="{{ $commodity }}" style="background: #041326;" {{ old('commodity') == $commodity ? 'selected' : '' }}>{{ $commodity }}</option>
                        @endforeach
                    </select>
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
                    <select name="unit" class="form-select bg-transparent text-white border-secondary @error('unit') is-invalid @enderror" required>
                        <option value="" disabled selected style="background: #041326;">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit }}" style="background: #041326;" {{ old('unit') == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                        @endforeach
                    </select>
                    @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fs-7 fw-medium text-uppercase">Container Type</label>
                    <select name="container_type" class="form-select bg-transparent text-white border-secondary @error('container_type') is-invalid @enderror" required>
                        <option value="" disabled selected style="background: #041326;">Select Type</option>
                        @foreach($containerTypes as $type)
                            <option value="{{ $type }}" style="background: #041326;" {{ old('container_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('container_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row g-4 mb-4 pt-3 border-top border-secondary border-opacity-25">
                <div class="col-md-6">
                    <h5 class="text-white fw-bold mb-3">Origin</h5>
                    <div class="mb-3">
                        <label class="form-label text-muted fs-7 fw-medium text-uppercase">Country</label>
                        <select name="origin_country" id="origin_country" class="form-select bg-transparent text-white border-secondary @error('origin_country') is-invalid @enderror" required>
                            <option value="" disabled selected style="background: #041326;">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country }}" style="background: #041326;" {{ old('origin_country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label text-muted fs-7 fw-medium text-uppercase">Port</label>
                        <select name="origin_port" id="origin_port" class="form-select bg-transparent text-white border-secondary @error('origin_port') is-invalid @enderror" required>
                            <option value="" disabled selected style="background: #041326;">Select Port</option>
                            @foreach($ports as $port)
                                <option value="{{ $port }}" style="background: #041326;" {{ old('origin_port') == $port ? 'selected' : '' }}>{{ $port }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="text-white fw-bold mb-3">Destination</h5>
                    <div class="mb-3">
                        <label class="form-label text-muted fs-7 fw-medium text-uppercase">Country</label>
                        <select name="destination_country" id="destination_country" class="form-select bg-transparent text-white border-secondary @error('destination_country') is-invalid @enderror" required>
                            <option value="" disabled selected style="background: #041326;">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country }}" style="background: #041326;" {{ old('destination_country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label text-muted fs-7 fw-medium text-uppercase">Port</label>
                        <select name="destination_port" id="destination_port" class="form-select bg-transparent text-white border-secondary @error('destination_port') is-invalid @enderror" required>
                            <option value="" disabled selected style="background: #041326;">Select Port</option>
                            @foreach($ports as $port)
                                <option value="{{ $port }}" style="background: #041326;" {{ old('destination_port') == $port ? 'selected' : '' }}>{{ $port }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4 pt-4 border-top border-secondary border-opacity-25">
                <x-button variant="primary" icon="check" type="submit">Deploy Shipment</x-button>
            </div>
        </form>
    </x-card>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const portsData = {!! isset($portsMapping) ? json_encode($portsMapping) : '[]' !!};
    
    function filterPorts(countrySelectId, portSelectId) {
        const countrySelect = document.getElementById(countrySelectId);
        const portSelect = document.getElementById(portSelectId);
        
        countrySelect.addEventListener('change', function() {
            const selectedCountry = this.value;
            const currentPort = portSelect.value; // Store currently selected port
            
            // Clear current options
            portSelect.innerHTML = '<option value="" disabled selected style="background: #041326;">Select Port</option>';
            
            // Filter ports
            const filteredPorts = portsData.filter(p => p.country === selectedCountry);
            
            // Re-populate options
            filteredPorts.forEach(port => {
                const option = document.createElement('option');
                option.value = port.name;
                option.style.background = '#041326';
                option.textContent = port.name;
                // Preserve selection if it matches
                if (port.name === currentPort) option.selected = true;
                portSelect.appendChild(option);
            });
        });
    }

    // Initialize sync logic
    if (portsData.length > 0) {
        filterPorts('origin_country', 'origin_port');
        filterPorts('destination_country', 'destination_port');
        
        // Trigger change initially to filter existing selections on validation failure
        if (document.getElementById('origin_country').value) {
            document.getElementById('origin_country').dispatchEvent(new Event('change'));
        }
        if (document.getElementById('destination_country').value) {
            document.getElementById('destination_country').dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endsection
