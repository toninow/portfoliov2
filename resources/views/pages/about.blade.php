@php
    use App\Support\Locale;
    use Illuminate\Support\Facades\Storage;

    $l = app()->getLocale();
    $headline = $profile->getTranslation('headline', $l) ?: __('portfolio.hero.eyebrow');
    $steps = __('portfolio.about_page.how_steps');
@endphp

<x-layout
    :title="__('portfolio.meta.about_title')"
    :description="__('portfolio.meta.about_description')"
    :absoluteTitle="true"
>
    <section class="section">
        <div class="container-page grid lg:grid-cols-[1fr_1.7fr] gap-10 lg:gap-14 items-start">
            @if($profile->avatar_path)
                <aside class="max-w-xs mx-auto lg:mx-0 lg:sticky lg:top-28">
                    <img src="{{ Storage::url($profile->avatar_path) }}" alt="{{ $profile->name }}"
                         class="w-full rounded-2xl border border-[var(--color-line)]" width="400" height="400">
                    @if($profile->cvAvailable())
                        <a href="{{ Locale::route('cv') }}" target="_blank" rel="noopener" class="mt-4 btn btn-primary w-full">{{ __('portfolio.nav.cv') }}</a>
                    @endif
                </aside>
            @endif

            <div class="min-w-0 max-w-3xl">
                <p class="eyebrow">{{ __('portfolio.sections.about') }}</p>
                <h1 class="mt-2 text-4xl sm:text-5xl font-bold">{{ $profile->name }}</h1>
                <p class="mt-3 text-base sm:text-lg font-mono text-[var(--color-brand-bright)]">{{ $headline }}</p>
                <p class="mt-5 text-lg text-[var(--color-muted)] leading-relaxed">{{ __('portfolio.about_page.intro') }}</p>

                <h2 class="mt-12 text-2xl font-bold">{{ __('portfolio.about_page.evolution_title') }}</h2>
                <div class="mt-4 space-y-4 text-[var(--color-muted)] leading-relaxed">
                    <p>{{ __('portfolio.about_page.evolution_p1') }}</p>
                    <p>{{ __('portfolio.about_page.evolution_p2') }}</p>
                </div>

                <h2 class="mt-12 text-2xl font-bold">{{ __('portfolio.about_page.current_title') }}</h2>
                <p class="mt-4 text-[var(--color-muted)] leading-relaxed">{{ __('portfolio.about_page.current_body') }}</p>

                <h2 class="mt-12 text-2xl font-bold">{{ __('portfolio.about_page.how_title') }}</h2>
                <p class="mt-4 text-[var(--color-muted)] leading-relaxed">{{ __('portfolio.about_page.how_body') }}</p>
                @if(is_array($steps))
                    <ol class="mt-6 grid sm:grid-cols-2 gap-3">
                        @foreach($steps as $index => $step)
                            <li class="card p-4 flex gap-3 items-start">
                                <span class="font-mono text-sm text-[var(--color-brand-bright)] shrink-0" aria-hidden="true">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-sm text-[var(--color-muted)]">{{ $step }}</span>
                            </li>
                        @endforeach
                    </ol>
                @endif

                @if($experiences->isNotEmpty())
                    <section id="experiencia" class="mt-14 scroll-mt-24" aria-labelledby="experiencia-title">
                        <p class="eyebrow">{{ __('portfolio.sections.experience_eyebrow') }}</p>
                        <h2 id="experiencia-title" class="mt-2 text-2xl sm:text-3xl font-bold">{{ __('portfolio.sections.experience') }}</h2>
                        <p class="mt-3 text-sm sm:text-base text-[var(--color-muted)] leading-relaxed mb-8 max-w-2xl">{{ __('portfolio.sections.experience_lead') }}</p>
                        <x-site.experience-timeline :experiences="$experiences" variant="full" />
                    </section>
                @endif

                @if($stackGroups->isNotEmpty() || $platformTechnologies->isNotEmpty() || $toolTechnologies->isNotEmpty() || $additionalTechnologies->isNotEmpty())
                    <div class="mt-14">
                        <x-site.tech-stack
                            :stack-groups="$stackGroups"
                            :platforms="$platformTechnologies"
                            :tools="$toolTechnologies"
                            :additional="$additionalTechnologies" />
                    </div>
                @endif

                @if($education->isNotEmpty())
                    <h2 class="mt-14 text-2xl font-bold">{{ __('portfolio.about_page.education') }}</h2>
                    <div class="mt-4 space-y-3">
                        @foreach($education as $edu)
                            <div class="card p-5">
                                <h3 class="font-display font-semibold">{{ $edu->getTranslation('title', $l) }}</h3>
                                @if($edu->institution)
                                    <p class="text-sm text-[var(--color-muted)]">
                                        @if($edu->institution_url)
                                            <a href="{{ $edu->institution_url }}"
                                               class="underline-offset-2 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]"
                                               target="_blank"
                                               rel="noopener noreferrer">
                                                {{ $edu->institution }}
                                            </a>
                                        @else
                                            {{ $edu->institution }}
                                        @endif
                                        @if($edu->start_year)
                                            · {{ $edu->start_year }}@if($edu->end_year)–{{ $edu->end_year }}@endif
                                        @endif
                                    </p>
                                @endif
                                @if($edu->getTranslation('description', $l))
                                    <p class="mt-1 text-sm text-[var(--color-muted)]">{{ $edu->getTranslation('description', $l) }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <h2 class="mt-14 text-2xl font-bold">{{ __('portfolio.about_page.languages') }}</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="chip">{{ __('portfolio.about_page.lang_es') }}</span>
                    <span class="chip">{{ __('portfolio.about_page.lang_en') }}</span>
                </div>

                @if($certifications->isNotEmpty())
                    <h2 class="mt-14 text-2xl font-bold">{{ __('portfolio.about_page.certifications') }}</h2>
                    <ul class="mt-4 flex flex-wrap gap-2">
                        @foreach($certifications as $cert)
                            <li class="chip">{{ $cert->getTranslation('name', $l) }}@if($cert->issuer) · {{ $cert->issuer }}@endif</li>
                        @endforeach
                    </ul>
                @endif

                <h2 class="mt-14 text-2xl font-bold">{{ __('portfolio.about_page.interest_title') }}</h2>
                <p class="mt-4 text-[var(--color-muted)] leading-relaxed">{{ __('portfolio.about_page.interest_body') }}</p>

                <h2 class="mt-12 text-2xl font-bold">{{ __('portfolio.about_page.next_title') }}</h2>
                <p class="mt-4 text-[var(--color-muted)] leading-relaxed">{{ __('portfolio.about_page.next_body') }}</p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ Locale::route('projects.index') }}" class="btn btn-primary">{{ __('portfolio.about_page.cta_projects') }}</a>
                    <a href="{{ Locale::route('contact') }}" class="btn btn-ghost">{{ __('portfolio.about_page.cta_contact') }}</a>
                </div>
            </div>
        </div>
    </section>
</x-layout>
