@php
    use App\Support\Locale;
    use App\Support\ProjectLifecycle;
    $l = app()->getLocale();
    $lifecycles = ProjectLifecycle::options($l);
@endphp

<x-layout :title="__('portfolio.projects.title')" :description="__('portfolio.projects.lead')">
    <section class="section">
        <div class="container-page">
            <p class="eyebrow">{{ __('portfolio.projects.page_eyebrow') }}</p>
            <h1 class="mt-2 text-4xl sm:text-5xl font-bold">{{ __('portfolio.projects.title') }}</h1>
            <p class="mt-4 max-w-2xl text-lg text-[var(--color-muted)]">{{ __('portfolio.projects.lead') }}</p>

            <form method="GET" action="{{ Locale::route('projects.index') }}" class="mt-10 space-y-3" role="search">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    <div class="lg:col-span-2">
                        <label for="q" class="sr-only">{{ __('portfolio.projects.search') }}</label>
                        <input id="q" type="search" name="q" value="{{ $filters['q'] ?? '' }}"
                               placeholder="{{ __('portfolio.projects.search_placeholder') }}"
                               class="input-field">
                    </div>
                    <div>
                        <label for="categoria" class="sr-only">{{ __('portfolio.projects.filter_category') }}</label>
                        <select id="categoria" name="categoria" class="input-field">
                            <option value="">{{ __('portfolio.projects.filter_category') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" @selected(($filters['categoria'] ?? '') === $cat->slug)>{{ $cat->getTranslation('name', $l) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="estado" class="sr-only">{{ __('portfolio.projects.filter_status') }}</label>
                        <select id="estado" name="estado" class="input-field">
                            <option value="">{{ __('portfolio.projects.filter_status') }}</option>
                            @foreach($lifecycles as $key => $label)
                                <option value="{{ $key }}" @selected(($filters['estado'] ?? '') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="anio" class="sr-only">{{ __('portfolio.projects.filter_year') }}</label>
                        <select id="anio" name="anio" class="input-field">
                            <option value="">{{ __('portfolio.projects.filter_year') }}</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" @selected((string)($filters['anio'] ?? '') === (string) $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        <label for="tipo" class="sr-only">{{ __('portfolio.projects.filter_type') }}</label>
                        <select id="tipo" name="tipo" class="input-field min-w-[10rem]">
                            <option value="">{{ __('portfolio.projects.filter_type') }}</option>
                            <option value="caso" @selected(($filters['tipo'] ?? '') === 'caso')>{{ __('portfolio.projects.type_case') }}</option>
                            <option value="archivo" @selected(($filters['tipo'] ?? '') === 'archivo')>{{ __('portfolio.projects.type_archive') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="orden" class="sr-only">{{ __('portfolio.projects.sort') }}</label>
                        <select id="orden" name="orden" class="input-field min-w-[10rem]">
                            <option value="relevancia" @selected(($filters['orden'] ?? 'relevancia') === 'relevancia')>{{ __('portfolio.projects.sort_relevance') }}</option>
                            <option value="fecha" @selected(($filters['orden'] ?? '') === 'fecha')>{{ __('portfolio.projects.sort_date') }}</option>
                            <option value="destacados" @selected(($filters['orden'] ?? '') === 'destacados')>{{ __('portfolio.projects.sort_featured') }}</option>
                        </select>
                    </div>
                    <details class="grow sm:grow-0">
                        <summary class="cursor-pointer text-sm text-[var(--color-muted)] hover:text-[var(--color-ink)] py-2">
                            {{ __('portfolio.projects.advanced_filters') }}
                        </summary>
                        <div class="mt-2">
                            <label for="tecnologia" class="sr-only">{{ __('portfolio.projects.filter_technology') }}</label>
                            <select id="tecnologia" name="tecnologia" class="input-field min-w-[14rem]">
                                <option value="">{{ __('portfolio.projects.filter_technology') }}</option>
                                @foreach($technologies as $tech)
                                    <option value="{{ $tech->slug }}" @selected(($filters['tecnologia'] ?? '') === $tech->slug)>{{ $tech->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </details>
                    <button type="submit" class="btn btn-primary">{{ __('portfolio.projects.search') }}</button>
                    @if($hasActiveFilters)
                        <a href="{{ Locale::route('projects.index') }}" class="btn btn-ghost">{{ __('portfolio.projects.clear') }}</a>
                    @endif
                </div>
            </form>

            <p class="mt-6 text-sm text-[var(--color-muted)] font-mono">
                {{ trans_choice('portfolio.projects.results', $totalVisible, ['count' => $totalVisible]) }}
            </p>

            @if($totalVisible === 0)
                <div class="mt-10 card p-12 text-center">
                    <p class="text-lg text-[var(--color-muted)]">{{ __('portfolio.projects.empty') }}</p>
                    <a href="{{ Locale::route('projects.index') }}" class="mt-4 inline-flex btn btn-ghost">{{ __('portfolio.projects.clear') }}</a>
                </div>
            @else
                @if(($filters['tipo'] ?? '') !== 'archivo' && $caseStudies->isNotEmpty())
                    <section class="mt-10" aria-labelledby="cases-heading">
                        <div class="flex items-end justify-between gap-4 mb-6">
                            <div>
                                <p class="eyebrow">{{ __('portfolio.projects.cases_eyebrow') }}</p>
                                <h2 id="cases-heading" class="mt-1 text-2xl sm:text-3xl font-bold">{{ __('portfolio.projects.cases_title') }}</h2>
                            </div>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-5">
                            @foreach($caseStudies as $project)
                                <x-site.project-card
                                    :project="$project"
                                    :size="$loop->first ? 'large' : ($loop->iteration <= 3 ? 'medium' : 'compact')"
                                    variant="case" />
                            @endforeach
                        </div>
                    </section>
                @endif

                @if(($filters['tipo'] ?? '') !== 'caso' && $archive && $archive->count())
                    <section class="mt-16" aria-labelledby="archive-heading">
                        <div class="mb-6">
                            <p class="eyebrow">{{ __('portfolio.projects.archive_eyebrow') }}</p>
                            <h2 id="archive-heading" class="mt-1 text-2xl sm:text-3xl font-bold">{{ __('portfolio.projects.archive_title') }}</h2>
                            <p class="mt-2 text-sm text-[var(--color-muted)] max-w-2xl">{{ __('portfolio.projects.archive_lead') }}</p>
                        </div>
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach($archive as $project)
                                <x-site.project-card :project="$project" size="compact" variant="archive" />
                            @endforeach
                        </div>
                        <div class="mt-10">{{ $archive->links() }}</div>
                    </section>
                @endif
            @endif

            <div class="mt-16 surface p-8 sm:p-10 text-center">
                <h2 class="text-xl sm:text-2xl font-bold">{{ __('portfolio.cta.title') }}</h2>
                <p class="mt-3 text-[var(--color-muted)] max-w-xl mx-auto">{{ __('portfolio.cta.body') }}</p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ Locale::route('contact') }}" class="btn btn-primary">{{ __('portfolio.cta.button') }}</a>
                    <a href="{{ Locale::route('about') }}" class="btn btn-ghost">{{ __('portfolio.cta.secondary') }}</a>
                </div>
            </div>
        </div>
    </section>
</x-layout>
