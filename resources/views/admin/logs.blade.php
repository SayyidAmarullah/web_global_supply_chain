@extends('layouts.admin')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <a href="{{ route('admin.index') }}" class="text-muted text-decoration-none hover-white"><span class="material-symbols-outlined fs-4">arrow_back</span></a>
                <span class="material-symbols-outlined text-warning fs-2">receipt_long</span> 
                System & Audit Logs
            </h1>
            <p class="text-muted fs-7 mt-1 ms-5">Traceable history of all system events and user actions</p>
        </div>
        <button class="btn btn-outline-danger d-flex align-items-center gap-2 px-3 py-2">
            <span class="material-symbols-outlined fs-5">delete_sweep</span> Clear Old Logs
        </button>
    </div>

    <!-- Search & Filter -->
    <div class="d-flex gap-2 mb-2">
        <input type="text" class="form-control bg-dark border-secondary text-white w-25" placeholder="Search logs...">
        <select class="form-select bg-dark border-secondary text-white w-auto">
            <option value="">All Types</option>
            <option value="Audit">Audit</option>
            <option value="Authentication">Authentication</option>
            <option value="Error">Error</option>
            <option value="System">System</option>
        </select>
        <button class="btn btn-outline-secondary">Filter</button>
    </div>

    <x-card title="System Log Records" icon="list_alt" glow="purple">
        <div class="table-responsive p-3">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr class="border-bottom border-secondary border-opacity-25">
                        <th class="text-muted fs-8 text-uppercase pb-2">Timestamp</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">User</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Type</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Action</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">IP Address</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-muted fs-7">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if($log->user)
                                <div class="text-white fw-bold">{{ $log->user->name }}</div>
                            @else
                                <div class="text-muted fw-bold">System</div>
                            @endif
                        </td>
                        <td>
                            @if($log->type == 'Error')
                                <span class="badge bg-danger bg-opacity-25 text-danger">{{ $log->type }}</span>
                            @elseif($log->type == 'Authentication')
                                <span class="badge bg-cyan bg-opacity-25 text-cyan-glow">{{ $log->type }}</span>
                            @elseif($log->type == 'Audit')
                                <span class="badge bg-purple bg-opacity-25 text-purple-neon">{{ $log->type }}</span>
                            @else
                                <span class="badge bg-secondary text-white">{{ $log->type }}</span>
                            @endif
                        </td>
                        <td class="text-white fw-medium">{{ $log->action }}</td>
                        <td class="text-muted fs-8">{{ $log->ip_address ?? 'N/A' }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary py-0 px-2" title="View Details" onclick="alert('{{ addslashes($log->description) }}')">
                                <span class="material-symbols-outlined fs-6">visibility</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <span class="material-symbols-outlined fs-1 mb-2">history</span>
                            <p class="mb-0">No logs found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </x-card>
</main>
@endsection
