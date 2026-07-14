@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <span class="material-symbols-outlined text-purple-neon fs-2">psychology</span> 
                AI Decision Engine
            </h1>
            <p class="text-muted fs-7 mt-1">Intelligent Trade & Logistics Recommendations</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ai.history') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 py-2">
                <span class="material-symbols-outlined fs-5">history</span> History
            </a>
            <a href="{{ route('ai.simulate') }}" class="btn btn-purple d-flex align-items-center gap-2 px-3 py-2">
                <span class="material-symbols-outlined fs-5">calculate</span> Profit Simulator
            </a>
        </div>
    </div>

    <!-- 1. AI Dashboard Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <x-card title="AI Confidence" icon="verified" glow="success">
                <div class="p-3 text-center">
                    <h2 class="display-5 text-success fw-bold mb-0">{{ $dashboard['confidence'] }}%</h2>
                    <span class="text-muted fs-8">Data accuracy & reliability</span>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="Highest Profit" icon="payments" glow="success">
                <div class="p-3 text-center">
                    <span class="d-block text-white fw-bold mb-1">{{ $dashboard['highest_profit_opp'] }}</span>
                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1">Recommended Action</span>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="Highest Risk" icon="warning" glow="danger">
                <div class="p-3 text-center">
                    <span class="d-block text-white fw-bold mb-1">{{ $dashboard['highest_risk'] }}</span>
                    <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 px-2 py-1">Avoid</span>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="Best Route" icon="alt_route" glow="cyan">
                <div class="p-3 text-center">
                    <span class="d-block text-white fw-bold mb-1">{{ $dashboard['best_route'] }}</span>
                    <span class="badge bg-primary bg-opacity-25 text-cyan-glow border border-primary border-opacity-25 px-2 py-1">Optimized</span>
                </div>
            </x-card>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- 2. AI Scoring (Radar Chart Placeholder / Bars) -->
        <div class="col-md-4">
            <x-card title="AI Scoring Engine" icon="radar" glow="purple">
                <div class="p-3">
                    @foreach($scores as $label => $score)
                        @php
                            $color = $score >= 80 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                            if($label == 'risk') $color = $score >= 70 ? 'danger' : ($score >= 40 ? 'warning' : 'success');
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-white fs-7 text-capitalize">{{ $label }} Score</span>
                                <span class="text-{{ $color }} fs-7 fw-bold">{{ $score }}/100</span>
                            </div>
                            <div class="progress bg-dark border border-secondary border-opacity-25" style="height: 6px;">
                                <div class="progress-bar bg-{{ $color }}" style="width: {{ $score }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>

        <!-- 3. Smart Recommendations -->
        <div class="col-md-8">
            <!-- Smart Export -->
            <x-card title="Smart Export Recommendations" icon="flight_takeoff" glow="success" class="mb-4">
                <div class="p-3">
                    <div class="table-responsive">
                        <table class="table table-dark table-borderless align-middle mb-0">
                            <thead>
                                <tr class="border-bottom border-secondary border-opacity-25">
                                    <th class="text-muted fs-8 text-uppercase pb-2">Target</th>
                                    <th class="text-muted fs-8 text-uppercase pb-2">Financials</th>
                                    <th class="text-muted fs-8 text-uppercase pb-2">Risk</th>
                                    <th class="text-muted fs-8 text-uppercase pb-2">Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exportRecs as $rec)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-white">{{ $rec['commodity'] }}</div>
                                        <div class="text-muted fs-8">To: {{ $rec['target_country'] }}</div>
                                    </td>
                                    <td>
                                        <div class="text-success fw-bold">{{ $rec['profit'] }} Profit</div>
                                        <div class="text-muted fs-8">Rev: {{ $rec['revenue'] }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $rec['risk'] == 'Low' ? 'success' : 'warning' }} bg-opacity-25 text-{{ $rec['risk'] == 'Low' ? 'success' : 'warning' }} border border-{{ $rec['risk'] == 'Low' ? 'success' : 'warning' }} border-opacity-25">
                                            {{ $rec['risk'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <p class="mb-0 fs-8 text-muted" style="max-width: 200px; white-space: normal;">{{ $rec['reason'] }}</p>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-card>

            <!-- Smart Import -->
            <x-card title="Smart Import Recommendations" icon="flight_land" glow="cyan">
                <div class="p-3">
                    <div class="table-responsive">
                        <table class="table table-dark table-borderless align-middle mb-0">
                            <thead>
                                <tr class="border-bottom border-secondary border-opacity-25">
                                    <th class="text-muted fs-8 text-uppercase pb-2">Supplier</th>
                                    <th class="text-muted fs-8 text-uppercase pb-2">Financials</th>
                                    <th class="text-muted fs-8 text-uppercase pb-2">Risk</th>
                                    <th class="text-muted fs-8 text-uppercase pb-2">Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($importRecs as $rec)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-white">{{ $rec['commodity'] }}</div>
                                        <div class="text-muted fs-8">From: {{ $rec['supplier_country'] }}</div>
                                    </td>
                                    <td>
                                        <div class="text-cyan-glow fw-bold">{{ $rec['savings'] }} Savings</div>
                                        <div class="text-muted fs-8">Cost: {{ $rec['purchase_cost'] }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $rec['risk'] == 'Low' ? 'success' : 'warning' }} bg-opacity-25 text-{{ $rec['risk'] == 'Low' ? 'success' : 'warning' }} border border-{{ $rec['risk'] == 'Low' ? 'success' : 'warning' }} border-opacity-25">
                                            {{ $rec['risk'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <p class="mb-0 fs-8 text-muted" style="max-width: 200px; white-space: normal;">{{ $rec['reason'] }}</p>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</main>
@endsection
