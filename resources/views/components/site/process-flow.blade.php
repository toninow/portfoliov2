@php
    $l = app()->getLocale();

    $steps = [
        ['key' => 'understand', 'd' => ['es' => 'Escucho el proceso real y a las personas que lo usan.', 'en' => 'I listen to the real process and the people using it.']],
        ['key' => 'audit', 'd' => ['es' => 'Reviso datos, sistemas y puntos de fricción.', 'en' => 'I review data, systems and friction points.']],
        ['key' => 'design', 'd' => ['es' => 'Defino una solución clara y mantenible.', 'en' => 'I define a clear, maintainable solution.']],
        ['key' => 'implement', 'd' => ['es' => 'Construyo, integro y pruebo con datos reales.', 'en' => 'I build, integrate and test with real data.']],
        ['key' => 'measure', 'd' => ['es' => 'Mido resultados y mejoro de forma continua.', 'en' => 'I measure results and improve continuously.']],
    ];

    $icons = [
        'understand' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>',
        'audit' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/><path d="M8 11h6M11 8v6"/></svg>',
        'design' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 7l10 5 10-5-10-5z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>',
        'implement' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m8 8-4 4 4 4"/><path d="m16 8 4 4-4 4"/><path d="m13 5-2 14"/></svg>',
        'measure' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m7 14 3-3 3 3 5-6"/><path d="M17 8h2v2"/></svg>',
    ];
@endphp

<ol class="process-flow" data-reveal-stagger>
    @foreach($steps as $i => $step)
        <li class="process-step">
            <div class="process-step__head">
                <span class="process-step__badge">{!! $icons[$step['key']] !!}</span>
            </div>
            <span class="process-step__num">{{ sprintf('%02d', $i + 1) }}</span>
            <h3 class="process-step__title">{{ __('portfolio.process.'.$step['key']) }}</h3>
            <p class="process-step__desc">{{ $step['d'][$l] }}</p>
        </li>
    @endforeach
</ol>
