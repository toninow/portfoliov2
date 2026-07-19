@props(['technologies'])

@php
    $l = app()->getLocale();

    // Each area gets its own accent colour + icon so the grid reads as a
    // colourful, scannable map of what Antonio works with.
    $areas = [
        'backend' => [
            'label' => ['es' => 'Backend', 'en' => 'Backend'],
            'accent' => '#3b82f6',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m8 8-4 4 4 4"/><path d="m16 8 4 4-4 4"/></svg>',
        ],
        'data' => [
            'label' => ['es' => 'Bases de datos', 'en' => 'Databases'],
            'accent' => '#a78bfa',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v6c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/><path d="M4 11v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/></svg>',
        ],
        'erp' => [
            'label' => ['es' => 'Integraciones y plataformas', 'en' => 'Integrations and platforms'],
            'accent' => '#34d399',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>',
        ],
        'infra' => [
            'label' => ['es' => 'Infraestructura', 'en' => 'Infrastructure'],
            'accent' => '#fbbf24',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="6" rx="2"/><rect x="3" y="14" width="18" height="6" rx="2"/><path d="M7 7h.01M7 17h.01"/></svg>',
        ],
        'frontend' => [
            'label' => ['es' => 'Frontend', 'en' => 'Frontend'],
            'accent' => '#22d3ee',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>',
        ],
        'ia' => [
            'label' => ['es' => 'Automatización y datos', 'en' => 'Automation and data'],
            'accent' => '#f472b6',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/><circle cx="12" cy="12" r="3.2"/></svg>',
        ],
        'tools' => [
            'label' => ['es' => 'Entorno y metodología', 'en' => 'Environment and methodology'],
            'accent' => '#38bdf8',
            'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4l-6 6a1.5 1.5 0 0 0 2.1 2.1l6-6a4 4 0 0 0 5.4-5.4l-2.5 2.5-2.1-2.1 2.5-2.5z"/></svg>',
        ],
    ];
@endphp

<div class="tech-grid" data-reveal-stagger>
    @foreach($areas as $key => $area)
        @if(isset($technologies[$key]) && count($technologies[$key]))
            <div class="tech-card" style="--accent: {{ $area['accent'] }};" tabindex="0">
                <div class="tech-card__head">
                    <span class="tech-card__icon" aria-hidden="true">{!! $area['icon'] !!}</span>
                    <h3 class="tech-card__title">{{ $area['label'][$l] }}</h3>
                    <span class="tech-card__count">{{ count($technologies[$key]) }}</span>
                </div>
                <div class="tech-card__chips">
                    @foreach($technologies[$key] as $tech)
                        <span class="tech-chip">{{ $tech->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</div>
