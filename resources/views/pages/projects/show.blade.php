@php
    use App\Support\Locale;
    use Illuminate\Support\Facades\Storage;
    $l = app()->getLocale();
    $seo = $project->seo ?? [];
    $sections = [
        'problem' => __('portfolio.projects.the_problem'),
        'context' => __('portfolio.projects.context'),
        'constraints' => __('portfolio.projects.constraints'),
        'solution' => __('portfolio.projects.the_solution'),
        'process' => __('portfolio.projects.process'),
        'decisions' => __('portfolio.projects.decisions'),
        'result' => __('portfolio.projects.result'),
        'improvements' => __('portfolio.projects.improvements'),
    ];
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'CreativeWork',
        'name' => $project->getTranslation('name', $l),
        'about' => $project->getTranslation('summary', $l),
        'url' => url()->current(),
    ];
@endphp

<x-layout :title="$seo['title'][$l] ?? $project->getTranslation('name', $l)"
          :description="$seo['description'][$l] ?? $project->getTranslation('summary', $l)"
          :ogImage="$project->main_image_path ? Storage::url($project->main_image_path) : null"
          :indexable="($seo['indexable'] ?? true)"
          :jsonLd="$jsonLd">

    <article class="section">
        <div class="container-page max-w-4xl">
            <nav class="text-sm text-[var(--color-muted)]" aria-label="Breadcrumb">
                <a href="{{ Locale::route('projects.index') }}" class="link-underline">{{ __('portfolio.nav.projects') }}</a>
                <span aria-hidden="true"> / </span>
                <span class="text-[var(--color-ink)]">{{ $project->getTranslation('name', $l) }}</span>
            </nav>

            <header class="mt-6">
                @if($project->category)<span class="chip">{{ $project->category->getTranslation('name', $l) }}</span>@endif
                <h1 class="mt-4 text-3xl sm:text-5xl font-bold text-balance">{{ $project->getTranslation('name', $l) }}</h1>
                @if($project->getTranslation('summary', $l))
                    <p class="mt-4 text-lg text-[var(--color-muted)]">{{ $project->getTranslation('summary', $l) }}</p>
                @endif

                <dl class="mt-6 flex flex-wrap gap-x-8 gap-y-3 text-sm">
                    @if($project->getTranslation('role', $l))
                        <div><dt class="font-mono text-xs text-[var(--color-brand-bright)] uppercase">{{ __('portfolio.projects.role') }}</dt><dd class="mt-1">{{ $project->getTranslation('role', $l) }}</dd></div>
                    @endif
                    @if($project->period || $project->year)
                        <div><dt class="font-mono text-xs text-[var(--color-brand-bright)] uppercase">{{ __('portfolio.projects.period') }}</dt><dd class="mt-1">{{ $project->period ?: $project->year }}</dd></div>
                    @endif
                    @if($project->url)
                        <div><dt class="font-mono text-xs text-[var(--color-brand-bright)] uppercase">Web</dt><dd class="mt-1"><a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="text-[var(--color-brand-bright)] link-underline">{{ __('portfolio.projects.visit') }}</a></dd></div>
                    @endif
                </dl>
            </header>

            @if($project->main_image_path)
                <img src="{{ Storage::url($project->main_image_path) }}" alt="{{ $project->getTranslation('name', $l) }}"
                     class="mt-8 w-full rounded-2xl border border-[var(--color-line)]" loading="lazy">
            @endif

            @if($project->technologies->isNotEmpty())
                <div class="mt-8">
                    <h2 class="font-mono text-xs uppercase tracking-wider text-[var(--color-brand-bright)]">{{ __('portfolio.projects.technologies') }}</h2>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($project->technologies as $tech)<span class="chip">{{ $tech->name }}</span>@endforeach
                    </div>
                </div>
            @endif

            <div class="mt-12 space-y-10">
                @foreach($sections as $field => $label)
                    @if($project->getTranslation($field, $l))
                        <section data-reveal>
                            <h2 class="text-xl sm:text-2xl font-bold">{{ $label }}</h2>
                            <div class="mt-3 text-[var(--color-muted)] leading-relaxed whitespace-pre-line">{{ $project->getTranslation($field, $l) }}</div>
                        </section>
                    @endif
                @endforeach
            </div>

            @if($project->metrics->isNotEmpty())
                <section class="mt-12">
                    <h2 class="text-xl sm:text-2xl font-bold mb-4">{{ __('portfolio.projects.metrics') }}</h2>
                    <div class="grid sm:grid-cols-3 gap-4">
                        @foreach($project->metrics as $metric)
                            <div class="card p-5">
                                <div class="font-display text-2xl font-bold text-[var(--color-brand-bright)]">{{ $metric->value }}<span class="text-base text-[var(--color-muted)]">{{ $metric->unit }}</span></div>
                                <div class="mt-1 text-sm">{{ $metric->getTranslation('name', $l) }}</div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($project->images->isNotEmpty())
                <section class="mt-12">
                    <h2 class="text-xl sm:text-2xl font-bold mb-4">{{ __('portfolio.projects.gallery') }}</h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach($project->images as $image)
                            <figure class="card overflow-hidden">
                                <img src="{{ Storage::url($image->path) }}" alt="{{ $image->getTranslation('alt', $l) }}" loading="lazy" class="w-full h-auto">
                                @if($image->getTranslation('caption', $l))<figcaption class="p-3 text-sm text-[var(--color-muted)]">{{ $image->getTranslation('caption', $l) }}</figcaption>@endif
                            </figure>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Prev / next --}}
            <nav class="mt-14 flex items-center justify-between gap-4 border-t border-[var(--color-line)] pt-8 text-sm" aria-label="Paginación de proyectos">
                <div>
                    @if($previous)
                        <a href="{{ Locale::route('projects.show', $previous) }}" class="group inline-flex items-center gap-2 text-[var(--color-muted)] hover:text-[var(--color-ink)]">
                            <span aria-hidden="true">←</span>
                            <span>{{ $previous->getTranslation('name', $l) }}</span>
                        </a>
                    @endif
                </div>
                <div class="text-right">
                    @if($next)
                        <a href="{{ Locale::route('projects.show', $next) }}" class="group inline-flex items-center gap-2 text-[var(--color-muted)] hover:text-[var(--color-ink)]">
                            <span>{{ $next->getTranslation('name', $l) }}</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    @endif
                </div>
            </nav>

            <div class="mt-12 surface p-8 text-center">
                <h2 class="text-xl font-bold">{{ __('portfolio.cta.button') }}</h2>
                <a href="{{ Locale::route('contact') }}" class="mt-4 inline-flex btn btn-primary">{{ __('portfolio.nav.cta') }}</a>
            </div>
        </div>
    </article>
</x-layout>
