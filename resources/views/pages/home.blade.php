@php
    use App\Support\Locale;
    use Illuminate\Support\Facades\Storage;

    $l = app()->getLocale();
    $eyebrow = $profile->getTranslation('headline', $l) ?: __('portfolio.hero.eyebrow');
    $lead = $profile->getTranslation('bio', $l) ?: __('portfolio.hero.lead');
    $availability = $profile->getTranslation('availability', $l) ?: __('portfolio.availability.available');
    $problems = __('portfolio.problems.items');
@endphp

<x-layout
    :title="__('portfolio.meta.home_title')"
    :description="__('portfolio.meta.home_description')"
    :absoluteTitle="true"
>

    {{-- 1 · Hero --}}
    <section class="relative overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -top-32 -left-32 w-[28rem] h-[28rem] rounded-full opacity-30 blur-3xl" style="background: radial-gradient(circle, rgba(59,130,246,.45), transparent 60%);"></div>
            <div class="absolute -bottom-32 -right-32 w-[24rem] h-[24rem] rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, rgba(34,211,238,.4), transparent 60%);"></div>
        </div>

        <div class="container-page section-hero grid lg:grid-cols-2 gap-10 lg:gap-14 items-center">
            <div data-hero class="min-w-0 max-w-xl lg:max-w-none">
                <p class="eyebrow">{{ $eyebrow }}</p>
                <h1 class="mt-4 text-3xl sm:text-4xl lg:text-[2.55rem] leading-[1.12] font-bold text-balance">
                    {{ __('portfolio.hero.title') }}
                </h1>
                <p class="mt-5 text-base sm:text-lg text-[var(--color-muted)] max-w-xl leading-relaxed">
                    {{ $lead }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#casos" class="btn btn-primary">{{ __('portfolio.hero.view_projects') }}</a>
                    <a href="{{ Locale::route('contact') }}" class="btn btn-ghost">{{ __('portfolio.hero.talk') }}</a>
                    @if($profile->cvAvailable())
                        <a href="{{ Locale::route('cv') }}" class="btn btn-ghost" target="_blank" rel="noopener">{{ __('portfolio.hero.download_cv') }}</a>
                    @endif
                </div>

                @if($availability)
                    <p class="mt-6 inline-flex items-center gap-2 text-sm text-[var(--color-muted)]">
                        <span class="relative flex w-2.5 h-2.5">
                            <span class="motion-safe:animate-ping absolute inline-flex h-full w-full rounded-full opacity-60" style="background: var(--color-positive);"></span>
                            <span class="relative inline-flex rounded-full w-2.5 h-2.5" style="background: var(--color-positive);"></span>
                        </span>
                        {{ $availability }}
                    </p>
                @endif
            </div>

            <div id="mapa-sistemas" class="min-w-0 scroll-mt-24">
                <x-site.system-map />
            </div>
        </div>
    </section>

    {{-- 2 · Case studies --}}
    @if($homeCases->isNotEmpty())
    <section id="casos" class="section scroll-mt-24">
        <div class="container-page">
            <div class="flex items-end justify-between gap-4 mb-10">
                <div>
                    <p class="eyebrow">{{ __('portfolio.projects.cases_eyebrow') }}</p>
                    <h2 class="mt-2 text-3xl sm:text-4xl font-bold">{{ __('portfolio.projects.cases_title') }}</h2>
                </div>
                <a href="{{ Locale::route('projects.index') }}" class="hidden sm:inline-flex btn btn-ghost">{{ __('portfolio.projects.all') }}</a>
            </div>
            <div class="grid sm:grid-cols-2 gap-5">
                @foreach($homeCases as $project)
                    <x-site.project-card
                        :project="$project"
                        :size="$loop->first ? 'large' : 'medium'"
                        variant="case" />
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 3 · Impact metrics --}}
    @if($impactMetrics->isNotEmpty())
    <section id="impacto" class="border-y border-[var(--color-line)] bg-[var(--color-surface)]/40 scroll-mt-24">
        <div class="container-page py-10">
            <p class="eyebrow">{{ __('portfolio.projects.home_impact') }}</p>
            <div class="home-impact mt-5">
                @foreach($impactMetrics as $metric)
                    <div class="home-impact__item" data-reveal>
                        <div class="home-impact__value">{{ $metric->displayValue() }}@if($metric->unit)<span class="text-sm font-normal text-[var(--color-muted)]"> {{ $metric->unit }}</span>@endif</div>
                        <div class="home-impact__label">{{ $metric->getTranslation('name', $l) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 4 · Problems I help solve --}}
    <section id="problemas" class="section bg-[var(--color-surface)]/40 border-y border-[var(--color-line)] scroll-mt-24">
        <div class="container-page">
            <h2 class="text-3xl sm:text-4xl font-bold max-w-2xl">{{ __('portfolio.problems.title') }}</h2>
            <p class="mt-4 text-[var(--color-muted)] max-w-2xl leading-relaxed">{{ __('portfolio.problems.lead') }}</p>
            <div class="mt-10 grid sm:grid-cols-2 gap-4">
                @foreach($problems as $problem)
                    <div data-reveal class="card p-6">
                        <h3 class="font-display font-semibold text-lg">{{ $problem['title'] }}</h3>
                        <p class="mt-2 text-sm text-[var(--color-muted)] leading-relaxed">{{ $problem['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 5 · How I work --}}
    <section id="como-trabajo" class="section bg-[var(--color-surface)]/40 border-y border-[var(--color-line)] scroll-mt-24">
        <div class="container-page">
            <h2 class="text-3xl sm:text-4xl font-bold mb-4">{{ __('portfolio.sections.process') }}</h2>
            <p class="text-[var(--color-muted)] max-w-2xl mb-10 leading-relaxed">{{ __('portfolio.process_lead') }}</p>
            <x-site.process-flow />
        </div>
    </section>

    {{-- 7 · Experience --}}
    @if($experiences->isNotEmpty())
    <section id="experiencia" class="section scroll-mt-24" aria-labelledby="experiencia-title">
        <div class="container-page">
            <p class="eyebrow">{{ __('portfolio.sections.experience_eyebrow') }}</p>
            <h2 id="experiencia-title" class="mt-2 text-3xl sm:text-4xl font-bold max-w-2xl">{{ __('portfolio.sections.experience') }}</h2>
            <p class="mt-4 text-[var(--color-muted)] max-w-2xl leading-relaxed mb-10">{{ __('portfolio.sections.experience_lead') }}</p>
            <x-site.experience-timeline :experiences="$experiences" />
        </div>
    </section>
    @endif

    {{-- 8 · About teaser --}}
    <section id="sobre" class="section bg-[var(--color-surface)]/40 border-y border-[var(--color-line)] scroll-mt-24">
        <div class="container-page grid lg:grid-cols-[1fr_1.5fr] gap-10 items-center">
            @if($profile->avatar_path)
                <div data-reveal class="max-w-xs mx-auto lg:mx-0">
                    <img src="{{ Storage::url($profile->avatar_path) }}" alt="{{ $profile->name }}"
                         class="w-full rounded-2xl border border-[var(--color-line)]" loading="lazy" width="400" height="400">
                </div>
            @endif
            <div class="max-w-2xl">
                <p class="eyebrow">{{ __('portfolio.sections.about') }}</p>
                <h2 class="mt-2 text-3xl sm:text-4xl font-bold">{{ $profile->name }}</h2>
                <div class="mt-5 space-y-4 text-[var(--color-muted)] leading-relaxed">
                    <p>{{ __('portfolio.about_home.p1') }}</p>
                    <p>{{ __('portfolio.about_home.p2') }}</p>
                    <p>{{ __('portfolio.about_home.p3') }}</p>
                </div>
                <a href="{{ Locale::route('about') }}" class="mt-6 inline-flex btn btn-ghost">{{ __('portfolio.about_home.cta') }}</a>
            </div>
        </div>
    </section>

    {{-- 9 · Dual CTA --}}
    <section id="contacto-cta" class="section scroll-mt-24">
        <div class="container-page">
            <div class="surface p-8 sm:p-12 text-center" style="box-shadow: var(--shadow-glow);">
                <h2 class="text-2xl sm:text-3xl font-bold max-w-3xl mx-auto text-balance">{{ __('portfolio.cta.title') }}</h2>
                <p class="mt-4 text-[var(--color-muted)] max-w-2xl mx-auto leading-relaxed">{{ __('portfolio.cta.body') }}</p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ Locale::route('contact') }}" class="btn btn-primary">{{ __('portfolio.cta.button') }}</a>
                    <a href="{{ Locale::route('about') }}" class="btn btn-ghost">{{ __('portfolio.cta.secondary') }}</a>
                </div>
            </div>
        </div>
    </section>

</x-layout>
