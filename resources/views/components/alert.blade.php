@props(['variant' => 'info', 'icon' => null])

@php
    $bgClasses = [
        'success' => 'bg-success bg-opacity-10 text-success border-success border-opacity-25',
        'danger'  => 'bg-danger bg-opacity-10 text-danger border-danger border-opacity-25',
        'warning' => 'bg-warning bg-opacity-10 text-warning border-warning border-opacity-25',
        'info'    => 'bg-info bg-opacity-10 text-info border-info border-opacity-25',
    ];

    $iconMap = [
        'success' => 'check_circle',
        'danger'  => 'error',
        'warning' => 'warning',
        'info'    => 'info',
    ];

    $activeClass = $bgClasses[$variant] ?? $bgClasses['info'];
    $activeIcon = $icon ?? ($iconMap[$variant] ?? null);
@endphp

<div {{ $attributes->merge(['class' => "alert border rounded-4 p-3 d-flex align-items-start gap-3 glass-panel $activeClass"]) }} role="alert">
    @if($activeIcon)
        <span class="material-symbols-outlined fs-5 mt-1">{{ $activeIcon }}</span>
    @endif
    
    <div>
        {{ $slot }}
    </div>
</div>
