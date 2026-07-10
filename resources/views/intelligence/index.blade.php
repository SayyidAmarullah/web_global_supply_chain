@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h3 class="text-white fw-bold tracking-tight mb-1">Global Intelligence Hub</h3>
            <span class="text-muted fs-7">Real-time macro-economic, weather, and commodity analytics</span>
        </div>
        <div class="d-flex gap-2">
            <div class="input-group" style="width: 300px;">
                <span class="input-group-text bg-transparent border-end-0 border-secondary text-muted">
                    <span class="material-symbols-outlined fs-5">search</span>
                </span>
                <input type="text" class="form-control bg-transparent text-white border-start-0 border-secondary" placeholder="Search Country or Commodity...">
            </div>
            <x-button variant="outline" icon="filter_list">Filter</x-button>
        </div>
    </div>

    <!-- Row 1: Global Risk & AI Recommendations -->
    <div class="row g-4 mb-2">
        <div class="col-md-4">
            <x-card title="Global Risk Score" icon="public" glow="warning">
                <div class="d-flex align-items-center justify-content-center py-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; border: 8px solid var(--warning); box-shadow: 0 0 20px rgba(245, 158, 11, 0.3);">
                        <h1 class="text-white fw-bold mb-0">{{ $risk['overall'] }}</h1>
                    </div>
                </div>
                <div class="text-center mt-2 mb-4">
                    <x-badge variant="warning">{{ $risk['level'] }} Risk Level</x-badge>
                </div>
                <div class="d-flex justify-content-between text-muted fs-8 mb-2">
                    <span>Weather</span>
                    <span class="text-warning">{{ $risk['breakdown']['weather'] }}</span>
                </div>
                <div class="d-flex justify-content-between text-muted fs-8 mb-2">
                    <span>Political</span>
                    <span class="text-white">{{ $risk['breakdown']['political'] }}</span>
                </div>
                <div class="d-flex justify-content-between text-muted fs-8">
                    <span>Port Congestion</span>
                    <span class="text-danger">{{ $risk['breakdown']['port'] }}</span>
                </div>
            </x-card>
        </div>

        <div class="col-md-8">
            <x-card title="AI Strategic Recommendations" icon="smart_toy" glow="purple">
                <div class="row g-4 p-2">
                    @foreach($recommendations as $rec)
                        <div class="col-md-6">
                            <div class="glass-pill p-4 h-100 border border-purple border-opacity-25">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <x-badge variant="purple">{{ $rec['type'] }}</x-badge>
                                    @if($rec['impact'] === 'High')
                                        <span class="text-danger fw-bold fs-8">High Impact</span>
                                    @else
                                        <span class="text-warning fw-bold fs-8">Medium Impact</span>
                                    @endif
                                </div>
                                <h5 class="text-white fw-bold mb-2">{{ $rec['title'] }}</h5>
                                <p class="text-muted fs-7 mb-4">{{ $rec['description'] }}</p>
                                <x-button variant="outline" class="w-100">Analyze Scenario</x-button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>

    <!-- Row 2: Country Intelligence -->
    <div class="row g-4 mb-2">
        <div class="col-12">
            <x-card title="Country Profile: {{ $country['name'] }}" icon="language" glow="cyan">
                <div class="row g-4 p-2">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <p class="text-muted fs-7 mb-1">GDP</p>
                            <h4 class="text-white fw-bold">{{ $country['gdp'] }} <span class="text-success fs-7">({{ $country['gdp_growth'] }})</span></h4>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted fs-7 mb-1">Inflation Rate</p>
                            <h5 class="text-white fw-bold">{{ $country['inflation'] }}</h5>
                        </div>
                        <div>
                            <p class="text-muted fs-7 mb-1">Currency Exchange</p>
                            <h5 class="text-cyan-glow fw-bold" style="color: var(--cyan-glow);">{{ $country['currency'] }} = {{ $country['exchange_rate'] }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3 border-start border-secondary border-opacity-25">
                        <div class="mb-3 ps-3">
                            <p class="text-muted fs-7 mb-1">Trade Balance</p>
                            <h4 class="text-success fw-bold">{{ $country['trade_balance'] }}</h4>
                        </div>
                        <div class="mb-3 ps-3">
                            <p class="text-muted fs-7 mb-1">Import Volume</p>
                            <h5 class="text-white fw-bold">{{ $country['import_volume'] }}</h5>
                        </div>
                        <div class="ps-3">
                            <p class="text-muted fs-7 mb-1">Export Volume</p>
                            <h5 class="text-white fw-bold">{{ $country['export_volume'] }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3 border-start border-secondary border-opacity-25">
                        <div class="mb-3 ps-3">
                            <p class="text-muted fs-7 mb-1">Opportunity Score</p>
                            <h4 class="text-white fw-bold">{{ $country['opportunity_score'] }}/100</h4>
                        </div>
                        <div class="mb-3 ps-3">
                            <p class="text-muted fs-7 mb-1">Political Stability</p>
                            <x-badge variant="success">{{ $country['political_stability'] }}</x-badge>
                        </div>
                        <div class="ps-3">
                            <p class="text-muted fs-7 mb-1">Economic Stability</p>
                            <x-badge variant="success">{{ $country['economic_stability'] }}</x-badge>
                        </div>
                    </div>
                    <div class="col-md-3 border-start border-secondary border-opacity-25">
                        <div class="ps-3">
                            <p class="text-muted fs-7 mb-2">Top Exports</p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach($country['top_exports'] as $export)
                                    <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary">{{ $export }}</span>
                                @endforeach
                            </div>
                            <p class="text-muted fs-7 mb-2">Major Partners</p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($country['major_partners'] as $partner)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $partner }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Row 3: Commodity & Port Intelligence -->
    <div class="row g-4">
        <div class="col-md-6">
            <x-card title="Commodity Markets" icon="inventory_2">
                <div class="table-responsive mt-3">
                    <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                        <thead>
                            <tr>
                                <th class="text-muted fs-8 text-uppercase border-secondary">Commodity</th>
                                <th class="text-muted fs-8 text-uppercase border-secondary">Price</th>
                                <th class="text-muted fs-8 text-uppercase border-secondary">Trend (24h)</th>
                                <th class="text-muted fs-8 text-uppercase border-secondary">Vol Risk</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @foreach($commodities as $item)
                                <tr>
                                    <td class="text-white fw-medium">{{ $item['name'] }}</td>
                                    <td class="text-white">${{ number_format($item['price'], 2) }} <span class="text-muted fs-8">{{ $item['unit'] }}</span></td>
                                    <td>
                                        @if($item['trend'] > 0)
                                            <span class="text-success fw-bold">+{{ $item['trend'] }}% ↑</span>
                                        @else
                                            <span class="text-danger fw-bold">{{ $item['trend'] }}% ↓</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['risk'] === 'High')
                                            <x-badge variant="danger">High</x-badge>
                                        @elseif($item['risk'] === 'Medium')
                                            <x-badge variant="warning">Med</x-badge>
                                        @else
                                            <x-badge variant="success">Low</x-badge>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <div class="col-md-6">
            <x-card title="Port Congestion & Weather" icon="anchor">
                <div class="table-responsive mt-3">
                    <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                        <thead>
                            <tr>
                                <th class="text-muted fs-8 text-uppercase border-secondary">Port</th>
                                <th class="text-muted fs-8 text-uppercase border-secondary">Wait Time</th>
                                <th class="text-muted fs-8 text-uppercase border-secondary">Congestion</th>
                                <th class="text-muted fs-8 text-uppercase border-secondary">Weather</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @foreach($ports as $port)
                                <tr>
                                    <td class="text-white fw-medium">
                                        {{ $port['name'] }}
                                        <div class="text-muted fs-8">{{ $port['country'] }}</div>
                                    </td>
                                    <td class="text-white">{{ $port['wait_time'] }}</td>
                                    <td>
                                        @if($port['congestion'] === 'High')
                                            <x-badge variant="danger">High</x-badge>
                                        @elseif($port['congestion'] === 'Medium')
                                            <x-badge variant="warning">Med</x-badge>
                                        @else
                                            <x-badge variant="success">Low</x-badge>
                                        @endif
                                    </td>
                                    <td class="text-white">
                                        <div class="d-flex align-items-center gap-1">
                                            @if($port['weather'] === 'Storm' || $port['weather'] === 'Rain')
                                                <span class="material-symbols-outlined text-info fs-6">storm</span>
                                            @else
                                                <span class="material-symbols-outlined text-warning fs-6">light_mode</span>
                                            @endif
                                            {{ $port['weather'] }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</main>
@endsection
