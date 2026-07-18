@php
    use App\Support\Locale;
    use Illuminate\Support\Facades\Storage;
    $l = app()->getLocale();
    $areaLabels = ['backend' => 'Backend', 'frontend' => 'Frontend', 'data' => 'Datos', 'erp' => 'ERP & ecommerce', 'infra' => 'Infraestructura', 'tools' => 'Herramientas'];
@endphp

<x-layout :title="__('portfolio.nav.about')" :description="$profile->getTranslation('bio', $l)">
    <section class="section">
        <div class="container-page grid lg:grid-cols-[1fr_1.6fr] gap-10 items-start">
            @if($profile->avatar_path)
                <div class="max-w-xs mx-auto lg:mx-0 lg:sticky lg:top-28">
                    <img src="{{ Storage::url($profile->avatar_path) }}" alt="{{ $profile->name }}" class="w-full rounded-2xl border border-[var(--color-line)]">
                    @if($profile->cvAvailable())
                        <a href="{{ Locale::route('cv') }}" target="_blank" rel="noopener" class="mt-4 btn btn-primary w-full">{{ __('portfolio.nav.cv') }}</a>
                    @endif
                </div>
            @endif
            <div>
                <p class="eyebrow">{{ __('portfolio.sections.about') }}</p>
                <h1 class="mt-2 text-4xl sm:text-5xl font-bold">{{ $profile->name }}</h1>
                <p class="mt-4 text-lg text-[var(--color-muted)]">{{ $profile->getTranslation('headline', $l) }}</p>

                @if($profile->getTranslation('about_long', $l))
                    <div class="mt-6 text-[var(--color-muted)] leading-relaxed whitespace-pre-line">{{ $profile->getTranslation('about_long', $l) }}</div>
                @endif

                @if($experiences->isNotEmpty())
                    <h2 class="mt-12 text-2xl font-bold">{{ __('portfolio.sections.experience') }}</h2>
                    <div class="mt-4 space-y-3">
                        @foreach($experiences as $exp)
                            <div class="card p-5">
                                <div class="font-mono text-xs text-[var(--color-muted)]">{{ $exp->start_date }}@if($exp->start_date) – @endif{{ $exp->is_current ? ($l==='es'?'Actualidad':'Present') : $exp->end_date }}</div>
                                <h3 class="mt-1 font-display font-semibold">{{ $exp->getTranslation('role', $l) }}@if($exp->company) · <span class="text-[var(--color-muted)]">{{ $exp->company }}</span>@endif</h3>
                                @if($exp->getTranslation('description', $l))<p class="mt-1 text-sm text-[var(--color-muted)]">{{ $exp->getTranslation('description', $l) }}</p>@endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($education->isNotEmpty())
                    <h2 class="mt-12 text-2xl font-bold">{{ $l==='es'?'Formación':'Education' }}</h2>
                    <div class="mt-4 space-y-3">
                        @foreach($education as $edu)
                            <div class="card p-5">
                                <h3 class="font-display font-semibold">{{ $edu->getTranslation('title', $l) }}</h3>
                                @if($edu->institution)<p class="text-sm text-[var(--color-muted)]">{{ $edu->institution }} @if($edu->start_year)· {{ $edu->start_year }}@if($edu->end_year)–{{ $edu->end_year }}@endif @endif</p>@endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($certifications->isNotEmpty())
                    <h2 class="mt-12 text-2xl font-bold">{{ $l==='es'?'Certificaciones':'Certifications' }}</h2>
                    <ul class="mt-4 flex flex-wrap gap-2">
                        @foreach($certifications as $cert)<li class="chip">{{ $cert->getTranslation('name', $l) }}@if($cert->issuer) · {{ $cert->issuer }}@endif</li>@endforeach
                    </ul>
                @endif

                @if($technologies->isNotEmpty())
                    <h2 class="mt-12 text-2xl font-bold">{{ __('portfolio.sections.technologies') }}</h2>
                    <div class="mt-4 grid sm:grid-cols-2 gap-4">
                        @foreach($technologies as $area => $techs)
                            <div class="card p-5">
                                <h3 class="font-mono text-xs uppercase tracking-wider text-[var(--color-brand-bright)]">{{ $areaLabels[$area] ?? $area }}</h3>
                                <div class="mt-2 flex flex-wrap gap-2">@foreach($techs as $tech)<span class="chip">{{ $tech->name }}</span>@endforeach</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layout>
