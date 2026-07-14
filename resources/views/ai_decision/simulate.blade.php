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
                            <span class="text-muted fs-8 d-block mb-1">Risk Assessment</span>
                            <span class="text-warning fw-bold fs-5">Medium</span>
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
}
// Trigger on load
document.addEventListener('DOMContentLoaded', calculatePreview);
</script>
@endsection
