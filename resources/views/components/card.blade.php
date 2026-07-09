@props(['title' => null, 'icon' => null, 'glow' => null])

<div {{ $attributes->merge(['class' => 'glass-panel p-4 h-100 position-relative rounded-4']) }}
    @if($glow) style="box-shadow: 0 0 20px rgba(var(--{{ $glow }}-rgb), 0.15);" @endif>
    
    @if($title || $icon || isset($header))
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                @if($icon)
                    <span class="material-symbols-outlined me-2 {{ $glow ? 'text-'.$glow : 'text-cyan-glow' }}">{{ $icon }}</span>
                @endif
                @if($title)
                    <h5 class="text-white fw-bold mb-0 tracking-tight">{{ $title }}</h5>
                @endif
            </div>
            
            @isset($header)
                <div>{{ $header }}</div>
            @endisset
        </div>
    @endif

    <div class="card-body p-0">
        {{ $slot }}
    </div>
</div>
