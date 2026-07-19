@php
    use App\Support\Locale;
    $l = app()->getLocale();
    $blocks = [
        'problems' => __('portfolio.services.solves'),
        'includes' => __('portfolio.services.includes'),
        'use_cases' => __('portfolio.services.use_cases'),
        'deliverables' => __('portfolio.services.deliverables'),
    ];
@endphp

<x-layout :title="__('portfolio.services.title')" :description="__('portfolio.services.lead')">
    <section class="section">
        <div class="container-page">
            <p class="eyebrow">{{ __('portfolio.services.page_eyebrow') }}</p>
            <h1 class="mt-2 text-4xl sm:text-5xl font-bold">{{ __('portfolio.services.title') }}</h1>
            <p class="mt-4 max-w-2xl text-lg text-[var(--color-muted)]">{{ __('portfolio.services.lead') }}</p>

            <div class="mt-12 grid gap-6 lg:grid-cols-2">
                @foreach($services as $service)
                    <article data-reveal class="card p-7 flex flex-col">
                        <h2 class="text-2xl font-display font-semibold">{{ $service->getTranslation('title', $l) }}</h2>
                        @if($service->getTranslation('summary', $l))
                            <p class="mt-3 text-[var(--color-muted)]">{{ $service->getTranslation('summary', $l) }}</p>
                        @endif

                        @foreach($blocks as $field => $label)
                            @php $items = $service->getTranslation($field, $l); @endphp
                            @if(!empty($items))
                                <div class="mt-5">
                                    <h3 class="font-mono text-xs uppercase tracking-wider text-[var(--color-brand-bright)]">{{ $label }}</h3>
                                    <ul class="mt-2 space-y-1 text-sm text-[var(--color-muted)]">
                                        @foreach((is_array($items) ? $items : preg_split('/\r?\n/', (string) $items)) as $item)
                                            @if(trim((string) $item) !== '')
                                                <li class="flex gap-2"><span aria-hidden="true" style="color: var(--color-cyan)">›</span><span>{{ $item }}</span></li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endforeach

                        @if($service->technologies->isNotEmpty())
                            <div class="mt-5 flex flex-wrap gap-2">
                                @foreach($service->technologies as $tech)<span class="chip">{{ $tech->name }}</span>@endforeach
                            </div>
                        @endif

                        @if($service->relatedProject)
                            <p class="mt-5 text-sm text-[var(--color-muted)]">
                                <span class="font-mono text-xs uppercase tracking-wider text-[var(--color-brand-bright)]">{{ __('portfolio.services.related_case') }}</span>
                                <a href="{{ Locale::route('projects.show', $service->relatedProject) }}" class="mt-1 block link-underline text-[var(--color-ink)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]">
                                    {{ $service->relatedProject->translated('name') }}
                                </a>
                            </p>
                        @endif

                        <a href="{{ Locale::route('contact') }}" class="mt-6 inline-flex btn btn-ghost self-start">{{ __('portfolio.services.cta') }}</a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</x-layout>
