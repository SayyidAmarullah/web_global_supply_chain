@extends('layouts.app')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <a href="{{ route('ai.index') }}" class="text-muted text-decoration-none hover-white"><span class="material-symbols-outlined fs-4">arrow_back</span></a>
                <span class="material-symbols-outlined text-white fs-2">history</span> 
                AI Recommendation History
            </h1>
            <p class="text-muted fs-7 mt-1 ms-5">Log of all intelligent routing and trade recommendations.</p>
        </div>
    </div>

    <x-card title="Past Recommendations" icon="format_list_bulleted" glow="purple">
        <div class="p-3">
            @if($recommendations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead>
                            <tr class="border-bottom border-secondary border-opacity-25">
                                <th class="text-muted fs-8 text-uppercase pb-2">Date</th>
                                <th class="text-muted fs-8 text-uppercase pb-2">Type</th>
                                <th class="text-muted fs-8 text-uppercase pb-2">Recommendation</th>
                                <th class="text-muted fs-8 text-uppercase pb-2">Expected Profit</th>
                                <th class="text-muted fs-8 text-uppercase pb-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recommendations as $rec)
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="text-white fs-7">{{ $rec->created_at->format('M d, Y') }}</td>
                                <td><span class="badge bg-secondary text-white">{{ ucfirst($rec->type) }}</span></td>
                                <td>
                                    <div class="fw-bold text-white">{{ $rec->recommended_commodity }} to {{ $rec->recommended_country }}</div>
                                    <div class="text-muted fs-8 text-truncate" style="max-width: 250px;">{{ $rec->reason }}</div>
                                </td>
                                <td class="text-success fw-bold">${{ number_format($rec->estimated_profit, 2) }}</td>
                                <td>
                                    @if($rec->status === 'Accepted')
                                        <span class="badge bg-success bg-opacity-25 text-success">Accepted</span>
                                    @elseif($rec->status === 'Rejected')
                                        <span class="badge bg-danger bg-opacity-25 text-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-25 text-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $recommendations->links() }}
                </div>
            @else
                <p class="text-muted fs-7 text-center my-4">No AI recommendations have been logged yet.</p>
            @endif
        </div>
    </x-card>
</main>
@endsection
