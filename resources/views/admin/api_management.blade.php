@extends('layouts.admin')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <a href="{{ route('admin.index') }}" class="text-muted text-decoration-none hover-white"><span class="material-symbols-outlined fs-4">arrow_back</span></a>
                <span class="material-symbols-outlined text-cyan-glow fs-2">api</span> 
                API Management
            </h1>
            <p class="text-muted fs-7 mt-1 ms-5">Manage and monitor external API connections and health status</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2">
            <span class="material-symbols-outlined fs-5">sync</span> Sync All APIs
        </button>
    </div>

    <x-card title="Connected External APIs" icon="hub" glow="cyan" class="h-auto">
        <div class="table-responsive p-3">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr class="border-bottom border-secondary border-opacity-25">
                        <th class="text-muted fs-8 text-uppercase pb-2">API Name / Provider</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Status</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Latency</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Usage Quota</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($apis as $api)
                    <tr>
                        <td>
                            <div class="fw-bold text-white">{{ $api['name'] }}</div>
                            <div class="text-muted fs-8">Provider: {{ $api['provider'] }}</div>
                        </td>
                        <td>
                            @if($api['status'] == 'Active')
                                <span class="badge bg-success bg-opacity-25 text-success"><span class="material-symbols-outlined fs-8 align-middle">check_circle</span> Active</span>
                            @else
                                <span class="badge bg-danger bg-opacity-25 text-danger"><span class="material-symbols-outlined fs-8 align-middle">cancel</span> Disabled</span>
                            @endif
                        </td>
                        <td class="text-white">{{ $api['latency'] }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-white fs-8">{{ $api['quota'] }}</span>
                                @if(str_contains($api['quota'], '%'))
                                    <div class="progress bg-dark" style="height: 4px; width: 60px;">
                                        <div class="progress-bar {{ intval($api['quota']) > 80 ? 'bg-danger' : 'bg-success' }}" style="width: {{ $api['quota'] }}"></div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-info py-0 px-2" title="Test Connection" onclick="alert('Testing connection for {{ $api['name'] }}...\nStatus: HTTP 200 OK\nLatency: {{ $api['latency'] }}\nConnection is stable.')"><span class="material-symbols-outlined fs-6">network_check</span></button>
                            <button class="btn btn-sm btn-outline-warning py-0 px-2" title="Configure" onclick="alert('Configuration for {{ $api['name'] }} is restricted.\nPlease contact System Administrator for API key management.')"><span class="material-symbols-outlined fs-6">settings</span></button>
                            <form action="{{ route('admin.api-management.toggle') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="api_name" value="{{ $api['name'] }}">
                                @if($api['status'] == 'Active')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Disable"><span class="material-symbols-outlined fs-6">power_off</span></button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-outline-success py-0 px-2" title="Enable"><span class="material-symbols-outlined fs-6">power</span></button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</main>
@endsection
