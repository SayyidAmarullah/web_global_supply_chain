@extends('layouts.admin')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <a href="{{ route('admin.master-data') }}" class="text-muted text-decoration-none hover-white"><span class="material-symbols-outlined fs-4">arrow_back</span></a>
                <span class="material-symbols-outlined text-cyan-glow fs-2">anchor</span> 
                Port Data Management
            </h1>
            <p class="text-muted fs-7 mt-1 ms-5">Manage and add global seaports</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2" data-bs-toggle="modal" data-bs-target="#addPortModal" onclick="setTimeout(initMap, 500)">
            <span class="material-symbols-outlined fs-5">add_location_alt</span> Add New Port
        </button>
    </div>

    <div class="flex-grow-1">
        <x-card title="Registered Ports" icon="anchor" glow="cyan" class="h-100">
            <!-- Filter Bar -->
            <div class="px-3 pt-3 pb-2 border-bottom border-secondary border-opacity-25">
                <form action="{{ route('admin.ports.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-muted"><span class="material-symbols-outlined fs-6">search</span></span>
                            <input type="text" name="search" class="form-control border-secondary text-white shadow-none" style="background-color: #121826;" placeholder="Search port name or code..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-muted"><span class="material-symbols-outlined fs-6">public</span></span>
                            <select name="country" class="form-select border-secondary text-white shadow-none" style="background-color: #121826;">
                                <option value="">All Countries</option>
                                @foreach($dbCountries as $c)
                                    <option value="{{ $c }}" {{ request('country') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 text-end d-flex gap-2">
                        <button type="submit" class="btn btn-outline-info w-100">Filter</button>
                        @if(request()->hasAny(['search', 'country']))
                            <a href="{{ route('admin.ports.index') }}" class="btn btn-outline-secondary px-3" title="Clear Filters"><span class="material-symbols-outlined fs-6 align-middle">close</span></a>
                        @endif
                    </div>
                </form>
            </div>
            
            <div class="table-responsive p-3 h-100 overflow-auto" style="max-height: calc(100vh - 270px);">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr class="border-bottom border-secondary border-opacity-25">
                        <th class="text-muted fs-8 text-uppercase pb-2">Port Name</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Code</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Country</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Coordinates</th>
                        <th class="text-muted fs-8 text-uppercase pb-2 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ports as $port)
                    <tr>
                        <td class="text-white fw-bold">{{ $port->name }}</td>
                        <td><span class="badge bg-secondary text-white">{{ $port->code }}</span></td>
                        <td class="text-muted">{{ $port->country }}</td>
                        <td class="text-muted fs-8">{{ number_format($port->latitude, 4) }}, {{ number_format($port->longitude, 4) }}</td>
                        <td class="text-end">
                            <form action="{{ route('admin.ports.delete', $port->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this port?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete">
                                    <span class="material-symbols-outlined fs-6">delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4 d-flex justify-content-center pagination-dark">
                {{ $ports->links('pagination::bootstrap-5') }}
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
        --bs-pagination-active-bg: rgba(13, 202, 240, 0.2);
        --bs-pagination-active-border-color: #0dcaf0;
        --bs-pagination-disabled-bg: transparent;
        --bs-pagination-disabled-border-color: rgba(255,255,255,0.05);
    }
    .pagination-dark .page-item.active .page-link {
        color: #0dcaf0;
        box-shadow: 0 0 10px rgba(13, 202, 240, 0.3);
    }
</style>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let mapInitialized = false;
    let pickerMap;
    let marker;

    function initMap() {
        if (mapInitialized) {
            pickerMap.invalidateSize();
            return;
        }
        
        pickerMap = L.map('portPickerMap').setView([20, 0], 2);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 20
        }).addTo(pickerMap);

        // Make the map dark but very readable using CSS filters on the tile pane
        const tilePane = pickerMap.getPanes().tilePane;
        tilePane.style.filter = 'invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%)';

        pickerMap.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            document.getElementById('latInput').value = lat.toFixed(6);
            document.getElementById('lngInput').value = lng.toFixed(6);
            
            if (marker) {
                pickerMap.removeLayer(marker);
            }
            
            marker = L.marker([lat, lng]).addTo(pickerMap);
        });
        
        mapInitialized = true;
    }
</script>
    <div class="modal fade" id="addPortModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-secondary shadow-lg" style="background-color: #0b0f19;">
                <form action="{{ route('admin.ports.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-secondary border-opacity-25" style="background-color: #121826;">
                        <h5 class="modal-title text-white d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-info">add_location_alt</span> Add Port via Map
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 text-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row g-4">
                            <!-- Left: Form -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Port Name</label>
                                    <input type="text" name="name" class="form-control border-secondary text-white shadow-none" style="background-color: #121826;" placeholder="e.g. Port of Singapore" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">UN/LOCODE (Port Code)</label>
                                    <input type="text" name="code" class="form-control border-secondary text-white shadow-none" style="background-color: #121826;" placeholder="e.g. SGSIN" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Country</label>
                                    <select name="country" class="form-select border-secondary text-white shadow-none" style="background-color: #121826;" required>
                                        <option value="">Select Country...</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country['name'] }}">{{ $country['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label text-muted">Latitude</label>
                                        <input type="text" id="latInput" name="latitude" class="form-control border-secondary text-white shadow-none" style="background-color: #1a2235;" readonly required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label text-muted">Longitude</label>
                                        <input type="text" id="lngInput" name="longitude" class="form-control border-secondary text-white shadow-none" style="background-color: #1a2235;" readonly required>
                                    </div>
                                </div>
                                <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 text-info fs-8 mt-4">
                                    <span class="material-symbols-outlined fs-6 align-middle me-1">info</span>
                                    Click anywhere on the map to automatically fill the coordinates.
                                </div>
                            </div>
                            
                            <!-- Right: Map -->
                            <div class="col-md-8">
                                <div id="portPickerMap" style="height: 400px; width: 100%; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 5px 15px rgba(0,0,0,0.3);"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary border-opacity-25" style="background-color: #121826;">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Save Port</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('addPortModal'));
            myModal.show();
            setTimeout(initMap, 500);
        });
    </script>
    @endif
@endsection
