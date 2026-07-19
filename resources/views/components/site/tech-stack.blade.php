@props([
    'stackGroups', // Collection keyed by area of Technology collections
    'platforms',   // Collection of Technology
    'tools',       // Collection of Technology
    'additional',  // Collection of Technology
])

@php
    use App\Support\Locale;
    use App\Support\TechnologyTaxonomy;
    use Illuminate\Support\Facades\Storage;

    $l = app()->getLocale();
@endphp

<section class="tech-stack" aria-labelledby="tech-stack-title">
    <p class="eyebrow">{{ __('portfolio.tech.eyebrow') }}</p>
    <h2 id="tech-stack-title" class="mt-2 text-2xl sm:text-3xl font-bold max-w-2xl">{{ __('portfolio.tech.title') }}</h2>
    <p class="mt-4 text-[var(--color-muted)] max-w-2xl leading-relaxed">{{ __('portfolio.tech.lead') }}</p>

    @if($stackGroups->isNotEmpty())
        <div class="tech-stack__groups mt-8">
            @foreach(TechnologyTaxonomy::PRIMARY_STACK_AREAS as $area)
                @php $items = $stackGroups->get($area, collect()); @endphp
                @continue($items->isEmpty())
                <article class="tech-stack__card">
                    <h3 class="tech-stack__card-title">{{ TechnologyTaxonomy::areaLabel($area, $l) }}</h3>
                    <p class="tech-stack__card-desc">{{ TechnologyTaxonomy::areaDescription($area, $l) }}</p>
                    <ul class="tech-stack__tags">
                        @foreach($items as $tech)
                            <li>
                                @if($tech->published_projects_count > 0)
                                    <a href="{{ $tech->projectsIndexUrl() }}" class="tech-stack__tag is-link">
                                        @if($tech->icon_path)
                                            <img src="{{ Storage::url($tech->icon_path) }}" alt="" width="16" height="16" class="tech-stack__icon" loading="lazy">
                                        @endif
                                        <span>{{ $tech->name }}</span>
                                    </a>
                                @else
                                    <span class="tech-stack__tag">
                                        @if($tech->icon_path)
                                            <img src="{{ Storage::url($tech->icon_path) }}" alt="" width="16" height="16" class="tech-stack__icon" loading="lazy">
                                        @endif
                                        <span>{{ $tech->name }}</span>
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    @php
                        $related = $items
                            ->flatMap(fn ($tech) => $tech->projects)
                            ->unique('id')
                            ->sortByDesc(fn ($p) => (int) $p->is_featured)
                            ->take(2)
                            ->values();
                    @endphp

                    @if($related->isNotEmpty())
                        <div class="tech-stack__related">
                            <p class="tech-stack__related-label">{{ __('portfolio.tech.applied_in') }}</p>
                            <ul class="tech-stack__related-list">
                                @foreach($related as $project)
                                    <li>
                                        <a href="{{ Locale::route('projects.show', $project) }}">{{ $project->translated('name') }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <a class="tech-stack__related-link" href="{{ Locale::route('projects.index') }}">{{ __('portfolio.tech.view_related') }}</a>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</section>

@if($platforms->isNotEmpty())
    <section class="tech-stack tech-stack--secondary mt-14" aria-labelledby="tech-platforms-title">
        <h2 id="tech-platforms-title" class="text-2xl font-bold">{{ __('portfolio.tech.platforms_title') }}</h2>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl leading-relaxed">{{ __('portfolio.tech.platforms_lead') }}</p>
        <div class="tech-stack__groups mt-6">
            <article class="tech-stack__card">
                <ul class="tech-stack__tags">
                    @foreach($platforms as $tech)
                        <li>
                            @if($tech->published_projects_count > 0)
                                <a href="{{ $tech->projectsIndexUrl() }}" class="tech-stack__tag is-link" title="{{ $tech->getTranslation('description', $l) }}">
                                    <span>{{ $tech->name }}</span>
                                    @if($tech->published_projects_count)
                                        <span class="tech-stack__tag-meta">{{ trans_choice('portfolio.tech.projects_count', $tech->published_projects_count, ['count' => $tech->published_projects_count]) }}</span>
                                    @endif
                                </a>
                            @else
                                <span class="tech-stack__tag"><span>{{ $tech->name }}</span></span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                @php
                    $platformProjects = $platforms->flatMap->projects->unique('id')->sortByDesc(fn ($p) => (int) $p->is_featured)->take(2);
                @endphp
                @if($platformProjects->isNotEmpty())
                    <div class="tech-stack__related">
                        <p class="tech-stack__related-label">{{ __('portfolio.tech.applied_in') }}</p>
                        <ul class="tech-stack__related-list">
                            @foreach($platformProjects as $project)
                                <li><a href="{{ Locale::route('projects.show', $project) }}">{{ $project->translated('name') }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </article>
        </div>
    </section>
@endif

@if($tools->isNotEmpty())
    <section class="tech-stack tech-stack--tools mt-14" aria-labelledby="tech-tools-title">
        <h2 id="tech-tools-title" class="text-2xl font-bold">{{ __('portfolio.tech.tools_title') }}</h2>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl leading-relaxed">{{ __('portfolio.tech.tools_lead') }}</p>
        <p class="mt-3 text-sm text-[var(--color-muted)] max-w-2xl leading-relaxed">{{ __('portfolio.tech.tools_note') }}</p>
        <ul class="tech-stack__tags mt-5">
            @foreach($tools as $tech)
                <li><span class="tech-stack__tag"><span>{{ $tech->name }}</span></span></li>
            @endforeach
        </ul>
    </section>
@endif

@if($additional->isNotEmpty())
    <section class="tech-stack tech-stack--additional mt-14" aria-labelledby="tech-additional-title">
        <h2 id="tech-additional-title" class="text-2xl font-bold">{{ __('portfolio.tech.additional_title') }}</h2>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl leading-relaxed">{{ __('portfolio.tech.additional_lead') }}</p>

        <button
            type="button"
            class="btn btn-ghost mt-5"
            data-tech-more
            aria-expanded="false"
            aria-controls="tech-additional-panel"
            id="tech-additional-toggle"
        >
            <span data-tech-more-label-closed>{{ __('portfolio.tech.show_more') }}</span>
            <span data-tech-more-label-open hidden>{{ __('portfolio.tech.show_less') }}</span>
        </button>

        <div id="tech-additional-panel" class="tech-stack__additional-panel mt-5" hidden>
            <ul class="tech-stack__tags">
                @foreach($additional as $tech)
                    <li>
                        @if($tech->published_projects_count > 0)
                            <a href="{{ $tech->projectsIndexUrl() }}" class="tech-stack__tag is-link">
                                <span>{{ $tech->name }}</span>
                            </a>
                        @else
                            <span class="tech-stack__tag"><span>{{ $tech->name }}</span></span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
