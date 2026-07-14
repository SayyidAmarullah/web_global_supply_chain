@extends('layouts.admin')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <span class="material-symbols-outlined text-danger fs-2">admin_panel_settings</span> 
                Administration & System Management
            </h1>
            <p class="text-muted fs-7 mt-1">Full control over users, API integrations, and system monitoring</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 py-2">
                <span class="material-symbols-outlined fs-5">settings</span> System Config
            </a>
            <a href="{{ route('admin.api-management') }}" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2">
                <span class="material-symbols-outlined fs-5">api</span> API Status
            </a>
        </div>
    </div>

    <!-- Top KPIs -->
    <div class="row g-4 mb-2">
        <div class="col-md-3">
            <x-card title="Total Users" icon="group" glow="cyan">
                <div class="px-3 pb-3">
                    <h2 class="text-white fw-bold mb-1">{{ $stats['total_users'] }}</h2>
                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25">{{ $stats['active_users'] }} Active</span>
                    <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 ms-1">{{ $stats['admins'] }} Admins</span>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="System Load (CPU)" icon="memory" glow="danger">
                <div class="px-3 pb-3">
                    <h2 class="text-danger fw-bold mb-1">{{ $stats['cpu_usage'] }}</h2>
                    <div class="progress bg-dark" style="height: 5px;">
                        <div class="progress-bar bg-danger" style="width: {{ str_replace('%','', $stats['cpu_usage']) }}%"></div>
                    </div>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="Memory Usage" icon="dns" glow="warning">
                <div class="px-3 pb-3">
                    <h2 class="text-warning fw-bold mb-1">{{ $stats['memory_usage'] }}</h2>
                    <div class="progress bg-dark" style="height: 5px;">
                        <div class="progress-bar bg-warning" style="width: {{ str_replace('%','', $stats['memory_usage']) }}%"></div>
                    </div>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card title="Database & API" icon="database" glow="success">
                <div class="px-3 pb-3">
                    <h2 class="text-success fw-bold mb-1">{{ $stats['db_status'] }}</h2>
                    <span class="text-muted fs-8">Storage: {{ $stats['storage_usage'] }}</span><br>
                    <span class="text-muted fs-8">API Status: <strong class="text-success">{{ $stats['api_status'] }}</strong></span>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Recent Logs & Activities -->
    <x-card title="Latest System Audit Logs" icon="history" glow="purple">
        <div class="table-responsive p-3">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr class="border-bottom border-secondary border-opacity-25">
                        <th class="text-muted fs-8 text-uppercase pb-2">Timestamp</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">User</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Type</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Action</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestLogs as $log)
                    <tr>
                        <td class="text-muted fs-7">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="text-white">{{ $log->user ? $log->user->name : 'System' }}</td>
                        <td>
                            @if($log->type == 'Error')
                                <span class="badge bg-danger bg-opacity-25 text-danger">{{ $log->type }}</span>
                            @elseif($log->type == 'Authentication')
                                <span class="badge bg-cyan bg-opacity-25 text-cyan-glow">{{ $log->type }}</span>
                            @else
                                <span class="badge bg-secondary text-white">{{ $log->type }}</span>
                            @endif
                        </td>
                        <td class="text-white">{{ $log->action }}</td>
                        <td class="text-muted">{{ $log->ip_address ?? 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No recent system logs.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3 text-end">
                <a href="{{ route('admin.logs') }}" class="text-cyan-glow text-decoration-none fs-8">View All Logs &rarr;</a>
            </div>
        </div>
    </x-card>

</main>
@endsection
