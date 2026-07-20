@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <a href="{{ route('ai.index') }}" class="text-muted text-decoration-none hover-white"><span class="material-symbols-outlined fs-4">arrow_back</span></a>
                <span class="material-symbols-outlined text-success fs-2">calculate</span> 
                Profit Simulation Engine
            </h1>
            <p class="text-muted fs-7 mt-1 ms-5">Simulate trade scenarios to calculate expected profit margins.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success bg-opacity-10 border-success border-opacity-25 text-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <!-- Input Form -->
        <div class="col-md-5">
            <x-card title="Simulation Parameters" icon="tune" glow="purple">
                <div class="p-3">
                    <form action="{{ route('ai.simulate') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted fs-8 text-uppercase">Simulation Name</label>
                            <input type="text" name="name" class="form-control bg-dark border-secondary text-white" placeholder="e.g. Q3 Wheat Export to Japan" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted fs-8 text-uppercase">Total Selling Price</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-muted">$</span>
                                    <input type="number" name="selling_price" id="selling_price" class="form-control bg-dark border-secondary text-white" value="100000" oninput="calculatePreview()" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted fs-8 text-uppercase">Total Purchase Cost</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-muted">$</span>
                                    <input type="number" name="purchase_cost" id="purchase_cost" class="form-control bg-dark border-secondary text-white" value="50000" oninput="calculatePreview()" required>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted fs-8 text-uppercase">Shipping Cost</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-muted">$</span>
                                    <input type="number" name="shipping_cost" id="shipping_cost" class="form-control bg-dark border-secondary text-white" value="5000" oninput="calculatePreview()" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted fs-8 text-uppercase">Insurance</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-muted">$</span>
                                    <input type="number" name="insurance_cost" id="insurance_cost" class="form-control bg-dark border-secondary text-white" value="1000" oninput="calculatePreview()" required>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-4">
                                <label class="form-label text-muted fs-8 text-uppercase">Import Tax</label>
                                <input type="number" name="import_tax" id="import_tax" class="form-control bg-dark border-secondary text-white" value="2000" oninput="calculatePreview()" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label text-muted fs-8 text-uppercase">Export Tax</label>
                                <input type="number" name="export_tax" id="export_tax" class="form-control bg-dark border-secondary text-white" value="500" oninput="calculatePreview()" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label text-muted fs-8 text-uppercase">Exchange Rate</label>
                                <input type="number" step="0.0001" name="exchange_rate" id="exchange_rate" class="form-control bg-dark border-secondary text-white" value="1.0000" oninput="calculatePreview()" required>
                            </div>
                        </div>

                        <!-- Supply Chain Risk Prediction Engine -->
                        <h6 class="text-white fw-bold mb-3 mt-4 border-bottom border-secondary pb-2">Supply Chain Risk Prediction (Weighted Risk Model)</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted fs-8 text-uppercase d-flex justify-content-between">
                                    <span>Weather Risk (30%)</span>
                                    <span id="weather_val" class="text-info">50%</span>
                                </label>
                                <input type="range" class="form-range custom-range" name="weather_risk" id="weather_risk" min="0" max="100" value="50" oninput="document.getElementById('weather_val').innerText = this.value + '%'; calculatePreview()">
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted fs-8 text-uppercase d-flex justify-content-between">
                                    <span>Inflation Risk (20%)</span>
                                    <span id="inflation_val" class="text-warning">30%</span>
                                </label>
                                <input type="range" class="form-range custom-range" name="inflation_risk" id="inflation_risk" min="0" max="100" value="30" oninput="document.getElementById('inflation_val').innerText = this.value + '%'; calculatePreview()">
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label text-muted fs-8 text-uppercase d-flex justify-content-between">
                                    <span>Political Risk (40%)</span>
                                    <span id="political_val" class="text-danger">60%</span>
                                </label>
                                <input type="range" class="form-range custom-range" name="political_risk" id="political_risk" min="0" max="100" value="60" oninput="document.getElementById('political_val').innerText = this.value + '%'; calculatePreview()">
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted fs-8 text-uppercase d-flex justify-content-between">
                                    <span>Currency Risk (10%)</span>
                                    <span id="currency_val" class="text-purple-neon" style="color: var(--purple-neon);">20%</span>
                                </label>
                                <input type="range" class="form-range custom-range" name="currency_risk" id="currency_risk" min="0" max="100" value="20" oninput="document.getElementById('currency_val').innerText = this.value + '%'; calculatePreview()">
                            </div>
                        </div>
                        <input type="hidden" name="total_risk" id="total_risk_input" value="0">

                        <button type="submit" class="btn btn-purple w-100 py-2 fw-bold">Save Simulation</button>
                    </form>
                </div>
            </x-card>
        </div>

        <!-- Live Output -->
        <div class="col-md-7">
            <x-card title="Live Projection" icon="monitoring" glow="cyan">
                <div class="p-4 text-center">
                    <h5 class="text-muted mb-2">Expected Net Profit</h5>
                    <h1 class="display-3 fw-bold text-success mb-0" id="preview_profit">$41,500.00</h1>
                    
                    <div class="d-inline-block mt-3 px-4 py-2 rounded glass-pill border border-success border-opacity-25">
                        <span class="text-muted fs-7 me-2">Profit Margin:</span>
                        <span class="text-success fw-bold fs-5" id="preview_margin">41.50%</span>
                    </div>

                    <hr class="border-secondary my-4">

                    <div class="row text-start g-3">
                        <div class="col-4">
                            <span class="text-muted fs-8 d-block mb-1">Total Revenue</span>
                            <span class="text-white fw-bold fs-5" id="preview_revenue">$100,000.00</span>
                        </div>
                        <div class="col-4">
                            <span class="text-muted fs-8 d-block mb-1">Total Costs</span>
                            <span class="text-danger fw-bold fs-5" id="preview_costs">$58,500.00</span>
                        </div>
                        <div class="col-4">
                            <span class="text-muted fs-8 d-block mb-1">Total Risk Score</span>
                            <span class="text-warning fw-bold fs-5" id="preview_risk">Medium (45%)</span>
                        </div>
                    </div>
                </div>
            </x-card>

            @if($simulations->count() > 0)
                <h5 class="text-white fw-bold mt-4 mb-3">Saved Simulations</h5>
                <div class="list-group list-group-flush gap-2">
                    @foreach($simulations as $sim)
                        <div class="list-group-item bg-dark border border-secondary border-opacity-25 rounded d-flex justify-content-between align-items-center p-3">
                            <div>
                                <h6 class="text-white fw-bold mb-1">{{ $sim->name }}</h6>
                                <span class="text-muted fs-8">{{ $sim->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="text-end">
                                <span class="d-block text-success fw-bold">${{ number_format($sim->expected_profit, 2) }}</span>
                                <span class="badge bg-success bg-opacity-25 text-success">{{ number_format($sim->margin_percentage, 1) }}% Margin</span>
                                <span class="badge bg-danger bg-opacity-25 text-danger ms-1">{{ number_format($sim->total_risk ?? 0, 1) }}% Risk</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</main>

<script>
function calculatePreview() {
    const sp = parseFloat(document.getElementById('selling_price').value) || 0;
    const pc = parseFloat(document.getElementById('purchase_cost').value) || 0;
    const sc = parseFloat(document.getElementById('shipping_cost').value) || 0;
    const ic = parseFloat(document.getElementById('insurance_cost').value) || 0;
    const it = parseFloat(document.getElementById('import_tax').value) || 0;
    const et = parseFloat(document.getElementById('export_tax').value) || 0;
    const er = parseFloat(document.getElementById('exchange_rate').value) || 1;

    const revenue = sp * er;
    const costs = pc + sc + ic + it + et;
    const profit = revenue - costs;
    const margin = revenue > 0 ? (profit / revenue) * 100 : 0;

    // Weighted Risk Calculation
    const wr = parseFloat(document.getElementById('weather_risk').value) || 0;
    const ir = parseFloat(document.getElementById('inflation_risk').value) || 0;
    const pr = parseFloat(document.getElementById('political_risk').value) || 0;
    const cr = parseFloat(document.getElementById('currency_risk').value) || 0;
    
    // Weights: Weather (0.3), Inflation (0.2), Political (0.4), Currency (0.1)
    const totalRisk = (wr * 0.3) + (ir * 0.2) + (pr * 0.4) + (cr * 0.1);
    document.getElementById('total_risk_input').value = totalRisk;

    document.getElementById('preview_revenue').innerText = '$' + revenue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('preview_costs').innerText = '$' + costs.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    const profitEl = document.getElementById('preview_profit');
    profitEl.innerText = '$' + profit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    if(profit >= 0) {
        profitEl.className = 'display-3 fw-bold text-success mb-0';
    } else {
        profitEl.className = 'display-3 fw-bold text-danger mb-0';
    }

    document.getElementById('preview_margin').innerText = margin.toFixed(2) + '%';
    
    const riskEl = document.getElementById('preview_risk');
    let riskLabel = 'Low';
    let riskClass = 'text-success';
    if(totalRisk > 33 && totalRisk <= 66) {
        riskLabel = 'Medium';
        riskClass = 'text-warning';
    } else if(totalRisk > 66) {
        riskLabel = 'High';
        riskClass = 'text-danger';
    }
    riskEl.className = riskClass + ' fw-bold fs-5';
    riskEl.innerText = riskLabel + ' (' + totalRisk.toFixed(1) + '%)';
}
// Trigger on load
document.addEventListener('DOMContentLoaded', calculatePreview);
</script>

<style>
.custom-range::-webkit-slider-thumb {
    background: var(--cyan-glow);
}
.custom-range::-moz-range-thumb {
    background: var(--cyan-glow);
}
</style>
@endsection
