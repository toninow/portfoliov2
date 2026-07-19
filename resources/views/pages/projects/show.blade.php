@php
    use App\Support\Locale;
    use App\Support\ProjectLifecycle;
    use Illuminate\Support\Facades\Storage;
    $l = app()->getLocale();
    $seo = $project->seo ?? [];
    $lifecycleLabel = ProjectLifecycle::label($project->lifecycle, $l);
    $outcome = $project->translated('outcome_headline');
    $workflow = collect($project->workflow_steps ?? [])->filter(fn ($s) => filled(data_get($s, 'label')))->values();
    $features = collect($project->features ?? [])->filter(fn ($s) => filled(data_get($s, 'title')))->values();
    $challenges = collect($project->challenges ?? [])->filter(fn ($s) => filled(data_get($s, 'difficulty')))->values();
    $decisions = collect($project->technical_decisions ?? [])->filter(fn ($s) => filled(data_get($s, 'decision')))->values();
    $qualResults = collect($project->qualitative_results ?? [])->filter(fn ($s) => filled(data_get($s, 'label')))->values();
    $links = collect($project->external_links ?? [])->filter(fn ($s) => filled(data_get($s, 'url')))->values();
    $publicMetrics = $project->metrics;
    $gallery = $project->relationLoaded('images')
        ? $project->images->where('is_visible', true)->values()
        : $project->visibleImages;

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'SoftwareApplication',
                'name' => $project->translated('name'),
                'description' => $project->translated('summary'),
                'url' => url()->current(),
                'applicationCategory' => $project->project_type,
                'dateCreated' => $project->year ? (string) $project->year : null,
            ],
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => __('portfolio.nav.projects'), 'item' => Locale::route('projects.index')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => $project->translated('name'), 'item' => url()->current()],
                ],
            ],
        ],
    ];
@endphp

<x-layout :title="$seo['title'][$l] ?? $project->translated('name')"
          :description="$seo['description'][$l] ?? $project->translated('summary')"
          :ogImage="$project->main_image_path ? Storage::url($project->main_image_path) : null"
          :indexable="($seo['indexable'] ?? true)"
          :jsonLd="$jsonLd">

    <article class="section">
        <div class="container-page max-w-5xl">
            <nav class="text-sm text-[var(--color-muted)]" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ Locale::route('projects.index') }}" class="link-underline">{{ __('portfolio.nav.projects') }}</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-[var(--color-ink)]">{{ $project->translated('name') }}</li>
                </ol>
            </nav>

            {{-- Hero --}}
            <header class="mt-6">
                <div class="flex flex-wrap gap-2">
                    @if($project->category)
                        <span class="chip">{{ $project->category->getTranslation('name', $l) }}</span>
                    @endif
                    @if($lifecycleLabel)
                        <span class="chip text-[var(--color-cyan)]">{{ $lifecycleLabel }}</span>
                    @endif
                    @if($project->isConfidential())
                        <span class="chip text-[var(--color-warning)]">{{ __('portfolio.projects.confidential') }}</span>
                    @endif
                </div>

                <h1 class="mt-4 text-3xl sm:text-5xl font-bold text-balance">{{ $project->translated('name') }}</h1>

                @if($outcome)
                    <p class="mt-4 text-xl text-[var(--color-brand-bright)] font-display font-semibold text-balance">{{ $outcome }}</p>
                @endif

                @if($project->translated('summary'))
                    <p class="mt-4 text-lg text-[var(--color-muted)] max-w-3xl">{{ $project->translated('summary') }}</p>
                @endif

                <dl class="mt-8 grid sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                    @if($project->translated('role'))
                        <div class="card p-4">
                            <dt class="font-mono text-xs text-[var(--color-brand-bright)] uppercase">{{ __('portfolio.projects.role') }}</dt>
                            <dd class="mt-1">{{ $project->translated('role') }}</dd>
                        </div>
                    @endif
                    @if($project->period || $project->year)
                        <div class="card p-4">
                            <dt class="font-mono text-xs text-[var(--color-brand-bright)] uppercase">{{ __('portfolio.projects.period') }}</dt>
                            <dd class="mt-1">{{ $project->period ?: $project->year }}@if($project->is_ongoing) · {{ __('portfolio.projects.ongoing') }}@endif</dd>
                        </div>
                    @endif
                    @if($project->project_type)
                        <div class="card p-4">
                            <dt class="font-mono text-xs text-[var(--color-brand-bright)] uppercase">{{ __('portfolio.projects.type') }}</dt>
                            <dd class="mt-1">{{ $project->project_type }}</dd>
                        </div>
                    @endif
                    @if($project->url || $links->isNotEmpty())
                        <div class="card p-4">
                            <dt class="font-mono text-xs text-[var(--color-brand-bright)] uppercase">{{ __('portfolio.projects.links') }}</dt>
                            <dd class="mt-1 space-y-1">
                                @if($project->url)
                                    <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="block text-[var(--color-brand-bright)] link-underline">{{ __('portfolio.projects.visit') }}</a>
                                @endif
                                @foreach($links as $link)
                                    <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="block text-[var(--color-brand-bright)] link-underline">{{ $link['label'] ?? __('portfolio.projects.visit') }}</a>
                                @endforeach
                            </dd>
                        </div>
                    @endif
                </dl>
            </header>

            @if($project->technologies->isNotEmpty())
                <div class="mt-8">
                    <h2 class="font-mono text-xs uppercase tracking-wider text-[var(--color-brand-bright)]">{{ __('portfolio.projects.technologies') }}</h2>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($project->technologies->take(6) as $tech)
                            <span class="chip">{{ $tech->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-12">
                <x-site.project-media :project="$project" :gallery="$gallery" />
            </div>

            {{-- Impact metrics --}}
            @if($publicMetrics->isNotEmpty())
                <section class="mt-14" data-reveal aria-labelledby="impact-heading">
                    <h2 id="impact-heading" class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.impact') }}</h2>
                    <div class="mt-5 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($publicMetrics as $metric)
                            <div class="card p-5">
                                <div class="font-display text-3xl font-bold text-[var(--color-brand-bright)]">
                                    {{ $metric->displayValue() }}@if($metric->unit)<span class="text-base text-[var(--color-muted)]"> {{ $metric->unit }}</span>@endif
                                </div>
                                <div class="mt-1 font-medium">{{ $metric->getTranslation('name', $l) }}</div>
                                @if($metric->getTranslation('description', $l))
                                    <p class="mt-1 text-sm text-[var(--color-muted)]">{{ $metric->getTranslation('description', $l) }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <div class="mt-14 space-y-12">
                @foreach([
                    'context' => __('portfolio.projects.context'),
                    'problem' => __('portfolio.projects.the_problem'),
                    'responsibilities' => __('portfolio.projects.responsibilities'),
                    'solution' => __('portfolio.projects.the_solution'),
                ] as $field => $label)
                    @if($project->translated($field))
                        <section data-reveal>
                            <h2 class="text-xl sm:text-2xl font-bold">{{ $label }}</h2>
                            <div class="mt-3 text-[var(--color-muted)] leading-relaxed whitespace-pre-line">{{ $project->translated($field) }}</div>
                        </section>
                    @endif
                @endforeach

                {{-- Workflow --}}
                @if($workflow->isNotEmpty())
                    <section data-reveal aria-labelledby="flow-heading">
                        <h2 id="flow-heading" class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.workflow') }}</h2>
                        <ol class="case-flow mt-6">
                            @foreach($workflow as $i => $step)
                                <li class="case-flow__step">
                                    <span class="case-flow__num">{{ sprintf('%02d', $i + 1) }}</span>
                                    <div>
                                        <h3 class="font-display font-semibold">{{ $step['label'] }}</h3>
                                        @if(!empty($step['description']))
                                            <p class="mt-1 text-sm text-[var(--color-muted)]">{{ $step['description'] }}</p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </section>
                @elseif($project->translated('process'))
                    <section data-reveal>
                        <h2 class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.process') }}</h2>
                        <div class="mt-3 text-[var(--color-muted)] leading-relaxed whitespace-pre-line">{{ $project->translated('process') }}</div>
                    </section>
                @endif

                {{-- Features --}}
                @if($features->isNotEmpty())
                    <section data-reveal>
                        <h2 class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.features') }}</h2>
                        <div class="mt-5 grid sm:grid-cols-2 gap-4">
                            @foreach($features as $feature)
                                <div class="card p-5">
                                    <h3 class="font-display font-semibold">{{ $feature['title'] }}</h3>
                                    @if(!empty($feature['description']))
                                        <p class="mt-2 text-sm text-[var(--color-muted)]">{{ $feature['description'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Architecture --}}
                @if($project->translated('architecture_description') || $project->technologies->count() > 4)
                    <section data-reveal>
                        <h2 class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.architecture') }}</h2>
                        @if($project->translated('architecture_description'))
                            <div class="mt-3 text-[var(--color-muted)] leading-relaxed whitespace-pre-line">{{ $project->translated('architecture_description') }}</div>
                        @endif
                        @if($project->technologies->isNotEmpty())
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach($project->technologies as $tech)
                                    <span class="chip">{{ $tech->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endif

                {{-- Technical decisions --}}
                @if($decisions->isNotEmpty())
                    <section data-reveal>
                        <h2 class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.decisions') }}</h2>
                        <div class="mt-5 space-y-4">
                            @foreach($decisions as $item)
                                <div class="card p-5">
                                    <h3 class="font-display font-semibold">{{ $item['decision'] }}</h3>
                                    @if(!empty($item['reason']))
                                        <p class="mt-2 text-sm text-[var(--color-muted)]"><span class="text-[var(--color-ink)]">{{ __('portfolio.projects.reason') }}:</span> {{ $item['reason'] }}</p>
                                    @endif
                                    @if(!empty($item['alternatives']))
                                        <p class="mt-1 text-sm text-[var(--color-muted)]"><span class="text-[var(--color-ink)]">{{ __('portfolio.projects.alternatives') }}:</span> {{ $item['alternatives'] }}</p>
                                    @endif
                                    @if(!empty($item['benefit']))
                                        <p class="mt-1 text-sm text-[var(--color-muted)]"><span class="text-[var(--color-ink)]">{{ __('portfolio.projects.benefit') }}:</span> {{ $item['benefit'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @elseif($project->translated('decisions'))
                    <section data-reveal>
                        <h2 class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.decisions') }}</h2>
                        <div class="mt-3 text-[var(--color-muted)] leading-relaxed whitespace-pre-line">{{ $project->translated('decisions') }}</div>
                    </section>
                @endif

                {{-- Challenges --}}
                @if($challenges->isNotEmpty())
                    <section data-reveal>
                        <h2 class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.challenges') }}</h2>
                        <div class="mt-5 space-y-4">
                            @foreach($challenges as $item)
                                <div class="card p-5 border-l-2 border-l-[var(--color-cyan)]">
                                    <p class="text-sm"><span class="font-mono text-xs uppercase tracking-wider text-[var(--color-cyan)]">{{ __('portfolio.projects.difficulty') }}</span><br>{{ $item['difficulty'] }}</p>
                                    @if(!empty($item['decision']))
                                        <p class="mt-3 text-sm"><span class="font-mono text-xs uppercase tracking-wider text-[var(--color-brand-bright)]">{{ __('portfolio.projects.decision_taken') }}</span><br>{{ $item['decision'] }}</p>
                                    @endif
                                    @if(!empty($item['outcome']))
                                        <p class="mt-3 text-sm text-[var(--color-muted)]"><span class="font-mono text-xs uppercase tracking-wider text-[var(--color-positive)]">{{ __('portfolio.projects.challenge_result') }}</span><br>{{ $item['outcome'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Results --}}
                @if($project->translated('result') || $qualResults->isNotEmpty())
                    <section data-reveal>
                        <h2 class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.result') }}</h2>
                        @if($project->translated('result'))
                            <div class="mt-3 text-[var(--color-muted)] leading-relaxed whitespace-pre-line">{{ $project->translated('result') }}</div>
                        @endif
                        @if($qualResults->isNotEmpty())
                            <ul class="mt-4 grid sm:grid-cols-2 gap-2">
                                @foreach($qualResults as $item)
                                    <li class="flex items-start gap-2 text-sm text-[var(--color-muted)]">
                                        <span class="mt-1 text-[var(--color-positive)]" aria-hidden="true">▸</span>
                                        <span>{{ $item['label'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </section>
                @endif

                @if($project->translated('constraints'))
                    <section data-reveal>
                        <h2 class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.constraints') }}</h2>
                        <div class="mt-3 text-[var(--color-muted)] leading-relaxed whitespace-pre-line">{{ $project->translated('constraints') }}</div>
                    </section>
                @endif

                @if($project->translated('learnings'))
                    <section data-reveal>
                        <h2 class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.learnings') }}</h2>
                        <div class="mt-3 text-[var(--color-muted)] leading-relaxed whitespace-pre-line">{{ $project->translated('learnings') }}</div>
                    </section>
                @endif

                @if($project->translated('improvements'))
                    <section data-reveal>
                        <h2 class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.improvements') }}</h2>
                        <div class="mt-3 text-[var(--color-muted)] leading-relaxed whitespace-pre-line">{{ $project->translated('improvements') }}</div>
                    </section>
                @endif
            </div>

            @if($related->isNotEmpty())
                <section class="related-slider mt-16" data-related-slider aria-labelledby="related-heading">
                    <div class="flex items-end justify-between gap-4 mb-5 px-1">
                        <h2 id="related-heading" class="text-xl font-bold">{{ __('portfolio.projects.related') }}</h2>
                        <p class="text-xs font-mono text-[var(--color-muted)] hidden sm:block">{{ __('portfolio.projects.related_hint') }}</p>
                    </div>
                    <div class="related-slider__viewport">
                        <div class="related-slider__track">
                            @foreach([1, 2] as $loopPass)
                                @foreach($related as $rel)
                                    <div class="related-slider__item" @if($loopPass === 2) aria-hidden="true" @endif>
                                        <x-site.project-card :project="$rel" size="compact" />
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            @if($previous || $next)
                <nav class="mt-14 flex items-center justify-between gap-4 border-t border-[var(--color-line)] pt-8 text-sm" aria-label="{{ __('portfolio.projects.nav_projects') }}">
                    <div>
                        @if($previous)
                            <a href="{{ Locale::route('projects.show', $previous) }}" class="inline-flex items-center gap-2 text-[var(--color-muted)] hover:text-[var(--color-ink)]">
                                <span aria-hidden="true">←</span>
                                <span>{{ $previous->getTranslation('name', $l) }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="text-right">
                        @if($next)
                            <a href="{{ Locale::route('projects.show', $next) }}" class="inline-flex items-center gap-2 text-[var(--color-muted)] hover:text-[var(--color-ink)]">
                                <span>{{ $next->getTranslation('name', $l) }}</span>
                                <span aria-hidden="true">→</span>
                            </a>
                        @endif
                    </div>
                </nav>
            @endif

            <div class="mt-12 surface p-8 text-center">
                <h2 class="text-xl font-bold">{{ __('portfolio.cta.button') }}</h2>
                <a href="{{ Locale::route('contact') }}" class="mt-4 inline-flex btn btn-primary">{{ __('portfolio.nav.cta') }}</a>
            </div>
        </div>
    </article>
</x-layout>
