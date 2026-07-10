@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4 position-relative">
    
    <div class="d-flex justify-content-between align-items-center mb-2 position-relative z-1">
        <div>
            <h3 class="text-white fw-bold tracking-tight mb-0">Global Mission Control</h3>
            <p class="text-muted fs-7 mb-0">Unified Strategic Overview & Trade Decision Support</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('map.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                <span class="material-symbols-outlined fs-5">explore</span>
                Open Global Map
            </a>
            <x-button variant="primary" icon="analytics">Generate Exec Report</x-button>
        </div>
    </div>

    <!-- Row 1: KPI Metrics -->
    <div class="row g-4 position-relative z-1">
        <div class="col-md-3">
            <x-card title="Shipment Volume" icon="local_shipping" glow="cyan">
                <div class="p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-white fw-bold mb-1">{{ number_format($totalShipments) }}</h2>
                        <span class="text-success fs-8 fw-bold">↑ 12% vs Last Month</span>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 border border-primary border-opacity-25" style="width: 50px; height: 50px;">
                        <span class="material-symbols-outlined text-cyan-glow">public</span>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="col-md-3">
            <x-card title="Active Transit" icon="sailing" glow="success">
                <div class="p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-white fw-bold mb-1">{{ number_format($activeTransit) }}</h2>
                        <span class="text-warning fs-8 fw-bold">Real-time GPS Active</span>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 border border-success border-opacity-25" style="width: 50px; height: 50px;">
                        <span class="material-symbols-outlined text-success">waves</span>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="col-md-3">
            <x-card title="Global Risk Index" icon="warning" glow="danger">
                <div class="p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-white fw-bold mb-1">{{ $riskData['overall'] }}/100</h2>
                        <span class="text-danger fs-8 fw-bold">{{ $riskData['level'] }} Risk Alert</span>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10 border border-danger border-opacity-25" style="width: 50px; height: 50px;">
                        <span class="material-symbols-outlined text-danger">radar</span>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="col-md-3">
            <x-card title="Trade Opportunity" icon="payments" glow="purple">
                <div class="p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-white fw-bold mb-1">{{ $country['opportunity_score'] }}/100</h2>
                        <span class="text-success fs-8 fw-bold">High Potential ({{ $country['name'] }})</span>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-purple bg-opacity-10 border border-purple border-opacity-25" style="width: 50px; height: 50px; background-color: rgba(139, 92, 246, 0.1); border-color: rgba(139, 92, 246, 0.25);">
                        <span class="material-symbols-outlined text-purple-neon" style="color: var(--purple-neon);">star</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Row 2: Charts & AI -->
    <div class="row g-4 position-relative z-1 mb-2">
        <!-- AI Recommendations -->
        <div class="col-md-8">
            <x-card title="AI Decision Support Engine" icon="smart_toy" glow="purple">
                <div class="p-4">
                    @foreach($aiRecommendations as $rec)
                        <div class="d-flex gap-3 mb-4 p-3 rounded glass-pill border border-purple border-opacity-25" style="background: rgba(139,92,246,0.05);">
                            <div class="mt-1">
                                <span class="material-symbols-outlined text-purple-neon fs-3" style="color: var(--purple-neon);">lightbulb</span>
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="text-white fw-bold mb-0">{{ $rec['title'] }}</h6>
                                    <x-badge variant="{{ $rec['impact'] === 'High' ? 'danger' : 'warning' }}">{{ $rec['impact'] }} Impact</x-badge>
                                </div>
                                <p class="text-muted fs-7 mb-2">{{ $rec['description'] }}</p>
                                <div class="d-flex gap-2">
                                    <x-button variant="outline" class="btn-sm border-secondary text-muted">Dismiss</x-button>
                                    <a href="{{ route('shipments.index') }}" class="btn btn-primary btn-sm px-3" style="background-color: var(--purple-neon); border-color: var(--purple-neon);">Take Action</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>

        <!-- Financial Chart -->
        <div class="col-md-4">
            <x-card title="Financial Trajectory" icon="stacked_line_chart">
                <div class="p-4 h-100">
                    <canvas id="financialChart" height="250"></canvas>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Row 3: Intelligence Grid -->
    <div class="row g-4 position-relative z-1 mb-4">
        
        <!-- Commodity Market -->
        <div class="col-md-4">
            <x-card title="Commodity Market" icon="inventory_2" glow="cyan">
                <div class="table-responsive p-3">
                    <table class="table table-dark table-hover mb-0" style="background: transparent;">
                        <tbody>
                            @foreach(array_slice($commodities, 0, 4) as $com)
                            <tr>
                                <td class="text-white border-secondary fs-7">{{ $com['name'] }}</td>
                                <td class="text-white border-secondary fw-bold fs-7">${{ $com['price'] }}</td>
                                <td class="border-secondary text-end fs-7">
                                    @if($com['trend'] > 0)
                                        <span class="text-success">+{{ $com['trend'] }}% ↑</span>
                                    @else
                                        <span class="text-danger">{{ $com['trend'] }}% ↓</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <!-- Currency Volatility -->
        <div class="col-md-4">
            <x-card title="Currency Volatility" icon="currency_exchange" glow="warning">
                <div class="p-3">
                    @foreach($currencies as $curr)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="text-white fw-bold d-block">{{ $curr['pair'] }}</span>
                                <span class="text-muted fs-8">Current Rate: {{ $curr['rate'] }}</span>
                            </div>
                            <span class="text-{{ $curr['status'] }} fw-bold">{{ $curr['trend'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>

        <!-- Latest News -->
        <div class="col-md-4">
            <x-card title="Global Intelligence News" icon="newspaper">
                <div class="p-3">
                    @foreach($news as $n)
                        <div class="mb-3">
                            <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary mb-1 fs-8">{{ $n['category'] }}</span>
                            <p class="text-white mb-1 fs-7 lh-sm">{{ $n['title'] }}</p>
                            <span class="text-muted fs-8">{{ $n['time'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financialChart').getContext('2d');
        
        let gradientProfit = ctx.createLinearGradient(0, 0, 0, 300);
        gradientProfit.addColorStop(0, 'rgba(56, 189, 248, 0.5)'); 
        gradientProfit.addColorStop(1, 'rgba(56, 189, 248, 0.0)');

        let gradientRisk = ctx.createLinearGradient(0, 0, 0, 300);
        gradientRisk.addColorStop(0, 'rgba(239, 68, 68, 0.3)'); 
        gradientRisk.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartMonths) !!},
                datasets: [
                    {
                        label: 'Profit',
                        data: {!! json_encode($chartProfits) !!},
                        borderColor: '#38BDF8',
                        backgroundColor: gradientProfit,
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Risk',
                        data: {!! json_encode($chartRisks) !!},
                        borderColor: '#EF4444',
                        backgroundColor: gradientRisk,
                        borderWidth: 2,
                        pointRadius: 0,
                        borderDash: [5, 5],
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { display: false },
                    y: { display: false, min: 0 }
                }
            }
        });
    });
</script>
@endsection
