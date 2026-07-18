@props([
    'project',
    'size' => 'compact',
])
@php
    use App\Support\Locale;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    $l = app()->getLocale();
    $large = $size === 'large';
    $img = $project->main_image_path ? Storage::url($project->main_image_path) : null;
    $statusLabels = ['published' => 'Publicado', 'draft' => 'Borrador', 'archived' => 'Archivado'];
    $insight = $project->getTranslation('problem', $l) ?: $project->getTranslation('summary', $l);
@endphp

<a href="{{ Locale::route('projects.show', $project) }}" data-reveal
   @class([
       'group card overflow-hidden flex flex-col transition-transform duration-200 hover:-translate-y-1',
       'sm:col-span-2 sm:flex-row' => $large,
   ])>
    <div @class(['relative overflow-hidden bg-[var(--color-elevated)]', 'sm:w-1/2 aspect-[16/10]' => $large, 'aspect-[16/10]' => !$large])>
        @if($img)
            <img src="{{ $img }}" alt="{{ $project->getTranslation('name', $l) }}" loading="lazy" decoding="async"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
        @else
            <div class="w-full h-full grid place-items-center text-[var(--color-muted)] font-mono text-xs">{{ $project->getTranslation('name', $l) }}</div>
        @endif

        @if($insight)
            <div class="proj-overlay" aria-hidden="true">
                <span class="proj-overlay__label">{{ __('portfolio.projects.the_problem') }}</span>
                <p class="proj-overlay__text">{{ Str::limit($insight, $large ? 180 : 120) }}</p>
            </div>
        @endif

        @if($project->category)
            <span class="absolute top-3 left-3 chip bg-[var(--color-bg)]/70">{{ $project->category->getTranslation('name', $l) }}</span>
        @endif
    </div>

    <div @class(['p-5 flex flex-col grow', 'sm:w-1/2 sm:p-7 sm:justify-center' => $large])>
        <div class="flex items-center gap-2 text-xs text-[var(--color-muted)] font-mono">
            @if($project->year)<span>{{ $project->year }}</span>@endif
            @if($project->project_type)<span aria-hidden="true">·</span><span>{{ $project->project_type }}</span>@endif
        </div>
        <h3 @class(['mt-2 font-display font-semibold text-[var(--color-ink)]', 'text-2xl' => $large, 'text-lg' => !$large])>
            {{ $project->getTranslation('name', $l) }}
        </h3>
        @if($project->getTranslation('summary', $l))
            <p class="mt-2 text-sm text-[var(--color-muted)] line-clamp-3">{{ $project->getTranslation('summary', $l) }}</p>
        @endif

        @if($project->technologies->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-1.5">
                @foreach($project->technologies->take($large ? 6 : 4) as $tech)
                    <span class="chip">{{ $tech->name }}</span>
                @endforeach
            </div>
        @endif

        <span class="mt-4 inline-flex items-center gap-1 text-sm text-[var(--color-brand-bright)] font-medium">
            {{ __('portfolio.projects.view_case') }}
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"
                 class="transition-transform group-hover:translate-x-1"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </span>
    </div>
</a>
