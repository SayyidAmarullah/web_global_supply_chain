@props(['variant' => 'info', 'icon' => null])

@php
    $bgClasses = [
        'success' => 'bg-success bg-opacity-10 text-success border-success border-opacity-25',
        'danger'  => 'bg-danger bg-opacity-10 text-danger border-danger border-opacity-25',
        'warning' => 'bg-warning bg-opacity-10 text-warning border-warning border-opacity-25',
        'info'    => 'bg-info bg-opacity-10 text-info border-info border-opacity-25',
        'primary' => 'bg-primary bg-opacity-10 text-primary border-primary border-opacity-25',
        'cyan'    => 'text-cyan-glow border-cyan',
        'purple'  => 'text-purple-neon border-purple',
    ];

    $iconMap = [
        'success' => 'check_circle',
        'danger'  => 'error',
        'warning' => 'warning',
        'info'    => 'info',
        'primary' => 'star',
    ];

    $activeClass = $bgClasses[$variant] ?? $bgClasses['info'];
    $activeIcon = $icon ?? ($iconMap[$variant] ?? null);
@endphp

<span {{ $attributes->merge(['class' => "badge border rounded-pill px-3 py-2 fw-medium d-inline-flex align-items-center gap-1 $activeClass"]) }}
      @if($variant === 'cyan') style="border-color: var(--cyan-glow); color: var(--cyan-glow);" @endif
      @if($variant === 'purple') style="border-color: var(--purple-neon); color: var(--purple-neon);" @endif>
    
    @if($activeIcon)
        <span class="material-symbols-outlined fs-7">{{ $activeIcon }}</span>
    @endif
    
    {{ $slot }}
</span>
