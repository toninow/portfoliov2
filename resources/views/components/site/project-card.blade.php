@props([
    'project',
    'size' => 'compact',
    'variant' => null, // case|archive|null (auto)
])
@php
    use App\Support\Locale;
    use App\Support\ProjectLifecycle;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $l = app()->getLocale();
    $variant = $variant ?? ($project->is_case_study && ! $project->is_archived ? 'case' : 'archive');
    $isCase = $variant === 'case';
    $large = $size === 'large' || ($isCase && $size !== 'compact');
    $img = $project->main_image_path ? Storage::url($project->main_image_path) : null;
    $outcome = $project->translated('outcome_headline') ?: $project->translated('summary');
    $problem = $project->translated('problem');
    $role = $project->translated('role');
    $publicMetrics = $project->relationLoaded('metrics')
        ? $project->metrics->where('is_public', true)->take(2)
        : collect();
    $lifecycleLabel = ProjectLifecycle::label($project->lifecycle, $l);
    $cta = $isCase ? __('portfolio.projects.view_case') : __('portfolio.projects.view_detail');
@endphp

<a href="{{ Locale::route('projects.show', $project) }}" data-reveal
   @class([
       'group project-card card overflow-hidden flex flex-col transition-transform duration-200 hover:-translate-y-1',
       'project-card--case' => $isCase,
       'project-card--archive' => ! $isCase,
       'sm:col-span-2 lg:flex-row' => $large && $isCase,
   ])>
    <div @class([
        'relative overflow-hidden bg-[var(--color-elevated)]',
        'lg:w-[46%] aspect-[16/10]' => $large && $isCase,
        'aspect-[16/10]' => ! ($large && $isCase),
    ])>
        @if($img)
            <img src="{{ $img }}" alt="{{ $project->translated('name') }}"
                 width="800" height="500" loading="lazy" decoding="async"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]">
        @else
            <div class="w-full h-full grid place-items-center text-[var(--color-muted)] font-mono text-xs px-4 text-center">
                {{ $project->translated('name') }}
            </div>
        @endif

        @if($problem && $isCase)
            <div class="proj-overlay" aria-hidden="true">
                <span class="proj-overlay__label">{{ __('portfolio.projects.the_problem') }}</span>
                <p class="proj-overlay__text">{{ Str::limit($problem, $large ? 180 : 110) }}</p>
            </div>
        @endif

        <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
            @if($project->category)
                <span class="chip bg-[var(--color-bg)]/80">{{ $project->category->getTranslation('name', $l) }}</span>
            @endif
            @if($project->isConfidential())
                <span class="chip bg-[var(--color-bg)]/80 text-[var(--color-warning)]">{{ __('portfolio.projects.confidential') }}</span>
            @endif
        </div>
    </div>

    <div @class(['p-5 flex flex-col grow', 'lg:w-[54%] lg:p-7 lg:justify-center' => $large && $isCase])>
        <div class="flex flex-wrap items-center gap-2 text-xs text-[var(--color-muted)] font-mono">
            @if($lifecycleLabel)
                <span class="text-[var(--color-cyan)]">{{ $lifecycleLabel }}</span>
            @endif
            @if($project->period || $project->year)
                <span aria-hidden="true">·</span>
                <span>{{ $project->period ?: $project->year }}</span>
            @endif
        </div>

        <h3 @class(['mt-2 font-display font-semibold text-[var(--color-ink)]', 'text-2xl' => $large, 'text-lg' => ! $large])>
            {{ $project->translated('name') }}
        </h3>

        @if($outcome)
            <p class="mt-2 text-sm text-[var(--color-muted)] {{ $isCase ? 'line-clamp-4' : 'line-clamp-2' }}">{{ $outcome }}</p>
        @endif

        @if($isCase && $publicMetrics->isNotEmpty())
            <dl class="mt-4 grid grid-cols-2 gap-3">
                @foreach($publicMetrics as $metric)
                    <div class="rounded-xl border border-[var(--color-line)] bg-[var(--color-elevated)]/50 px-3 py-2">
                        <dt class="font-display text-lg font-bold text-[var(--color-brand-bright)] leading-none">
                            {{ $metric->displayValue() }}@if($metric->unit)<span class="text-xs text-[var(--color-muted)] font-normal"> {{ $metric->unit }}</span>@endif
                        </dt>
                        <dd class="mt-1 text-xs text-[var(--color-muted)]">{{ $metric->getTranslation('name', $l) }}</dd>
                    </div>
                @endforeach
            </dl>
        @endif

        @if($isCase && $role)
            <p class="mt-3 text-xs text-[var(--color-muted)]">
                <span class="font-mono uppercase tracking-wider text-[var(--color-brand-bright)]">{{ __('portfolio.projects.role') }}</span>
                <span class="ml-1">{{ Str::limit($role, 90) }}</span>
            </p>
        @endif

        @if($project->technologies->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-1.5">
                @foreach($project->technologies->take($isCase ? 4 : 3) as $tech)
                    <span class="chip">{{ $tech->name }}</span>
                @endforeach
            </div>
        @endif

        <span class="mt-auto pt-4 inline-flex items-center gap-1 text-sm text-[var(--color-brand-bright)] font-medium">
            {{ $cta }}
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"
                 class="transition-transform group-hover:translate-x-1"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </span>
    </div>
</a>
