@props(['variant' => 'primary', 'type' => 'button', 'icon' => null, 'loading' => false])

@php
    $baseClasses = 'btn rounded-pill px-4 py-2 fw-medium d-inline-flex align-items-center justify-content-center gap-2 transition-all';
    
    $variants = [
        'primary' => 'btn-primary shadow-sm hover-neon-text',
        'secondary' => 'btn-secondary text-white',
        'outline' => 'btn-outline-light text-muted hover-text-dark',
        'danger' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-50',
        'warning' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-50',
        'success' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-50',
        'ghost' => 'btn-link text-muted text-decoration-none hover-neon-text',
    ];

    $btnClass = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $btnClass]) }}>
    @if($icon && !$loading)
        <span class="material-symbols-outlined fs-6">{{ $icon }}</span>
    @endif
    
    @if($loading)
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
    @endif

    {{ $slot }}
</button>
