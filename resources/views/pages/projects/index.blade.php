@php
    use App\Support\Locale;
    $l = app()->getLocale();
@endphp

<x-layout :title="__('portfolio.projects.title')" :description="__('portfolio.projects.lead')">
    <section class="section">
        <div class="container-page">
            <p class="eyebrow">{{ __('portfolio.nav.projects') }}</p>
            <h1 class="mt-2 text-4xl sm:text-5xl font-bold">{{ __('portfolio.projects.title') }}</h1>
            <p class="mt-4 max-w-2xl text-lg text-[var(--color-muted)]">{{ __('portfolio.projects.lead') }}</p>

            {{-- Filters --}}
            <form method="GET" action="{{ Locale::route('projects.index') }}" class="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-1">
                    <label for="q" class="sr-only">{{ __('portfolio.projects.search') }}</label>
                    <input id="q" type="search" name="q" value="{{ $filters['q'] ?? '' }}"
                           placeholder="{{ __('portfolio.projects.search_placeholder') }}"
                           class="w-full rounded-lg bg-[var(--color-surface)] border border-[var(--color-line)] px-4 py-3 text-base text-[var(--color-ink)] placeholder:text-[var(--color-muted)]">
                </div>
                <div>
                    <label for="categoria" class="sr-only">{{ __('portfolio.projects.filter_category') }}</label>
                    <select id="categoria" name="categoria" class="w-full rounded-lg bg-[var(--color-surface)] border border-[var(--color-line)] px-4 py-3 text-base">
                        <option value="">{{ __('portfolio.projects.filter_category') }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" @selected(($filters['categoria'] ?? '') === $cat->slug)>{{ $cat->getTranslation('name', $l) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tecnologia" class="sr-only">{{ __('portfolio.projects.filter_technology') }}</label>
                    <select id="tecnologia" name="tecnologia" class="w-full rounded-lg bg-[var(--color-surface)] border border-[var(--color-line)] px-4 py-3 text-base">
                        <option value="">{{ __('portfolio.projects.filter_technology') }}</option>
                        @foreach($technologies as $tech)
                            <option value="{{ $tech->slug }}" @selected(($filters['tecnologia'] ?? '') === $tech->slug)>{{ $tech->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <label for="anio" class="sr-only">{{ __('portfolio.projects.filter_year') }}</label>
                    <select id="anio" name="anio" class="w-full rounded-lg bg-[var(--color-surface)] border border-[var(--color-line)] px-4 py-3 text-base">
                        <option value="">{{ __('portfolio.projects.filter_year') }}</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" @selected((string)($filters['anio'] ?? '') === (string)$year)>{{ $year }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary shrink-0" aria-label="{{ __('portfolio.projects.search') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    </button>
                </div>
            </form>
            @if(array_filter($filters))
                <a href="{{ Locale::route('projects.index') }}" class="mt-3 inline-block text-sm text-[var(--color-brand-bright)] link-underline">{{ __('portfolio.projects.clear') }}</a>
            @endif

            {{-- Grid --}}
            @if($projects->isEmpty())
                <div class="mt-12 card p-12 text-center">
                    <p class="text-lg text-[var(--color-muted)]">{{ __('portfolio.projects.empty') }}</p>
                    <a href="{{ Locale::route('projects.index') }}" class="mt-4 inline-flex btn btn-ghost">{{ __('portfolio.projects.clear') }}</a>
                </div>
            @else
                <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($projects as $project)
                        <x-site.project-card :project="$project" size="compact" />
                    @endforeach
                </div>
                <div class="mt-10">{{ $projects->links() }}</div>
            @endif
        </div>
    </section>
</x-layout>
