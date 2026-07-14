@extends('layouts.admin')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <a href="{{ route('admin.index') }}" class="text-muted text-decoration-none hover-white"><span class="material-symbols-outlined fs-4">arrow_back</span></a>
                <span class="material-symbols-outlined text-warning fs-2">dns</span> 
                Master Data Management
            </h1>
            <p class="text-muted fs-7 mt-1 ms-5">Manage reference data for commodities, ports, and countries</p>
        </div>
    </div>

    <div class="row g-4">
        @foreach($masterData as $key => $count)
        <div class="col-md-4">
            <x-card title="{{ $key }}" icon="table_chart" glow="cyan">
                <div class="p-4 text-center">
                    <h2 class="display-4 fw-bold text-white mb-0">{{ number_format($count) }}</h2>
                    <span class="text-muted fs-7 d-block mt-2">Active Records</span>
                    <button class="btn btn-outline-primary mt-3 w-100">Manage {{ $key }}</button>
                </div>
            </x-card>
        </div>
        @endforeach
    </div>
</main>
@endsection
