@props(['technologies'])

@php
    use App\Support\TechnologyTaxonomy;
    $l = app()->getLocale();
    $order = ['backend', 'data', 'web', 'platforms', 'infra', 'tools', 'additional'];
@endphp

{{-- Compact fallback grid (home/legacy). Prefer <x-site.tech-stack> on About. --}}
<div class="tech-grid" data-reveal-stagger>
    @foreach($order as $key)
        @if(isset($technologies[$key]) && count($technologies[$key]))
            <div class="tech-card" style="--accent: var(--color-brand-bright);" tabindex="0">
                <div class="tech-card__head">
                    <h3 class="tech-card__title">{{ TechnologyTaxonomy::areaLabel($key, $l) }}</h3>
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
