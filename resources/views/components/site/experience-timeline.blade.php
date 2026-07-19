@props([
    'experiences',
    'variant' => 'full', // compact|full
])

@php
    use App\Support\Locale;
    use Illuminate\Support\Str;

    $l = app()->getLocale();
    $isCompact = $variant === 'compact';
    $currentLabel = __('portfolio.experience.current_role');
@endphp

<ol class="timeline @if($isCompact) timeline--compact @endif" data-reveal-stagger>
    @foreach($experiences as $exp)
        @php
            $index = $loop->index;
            $side = $index % 2 === 0 ? 'left' : 'right';
            $role = $exp->getTranslation('role', $l);
            $description = $exp->getTranslation('description', $l);
            $paragraphs = filled($description)
                ? array_values(array_filter(preg_split("/\n\s*\n/", trim($description)) ?: []))
                : [];
            if ($isCompact && $paragraphs !== []) {
                $excerpt = Str::limit(preg_replace('/\s+/', ' ', $paragraphs[0]), 220);
                $paragraphs = [$excerpt];
            }
            $location = $exp->displayLocation($l);
            $modality = $exp->modalityLabel($l);
            $period = $exp->periodLabel($l);
            $techTags = $exp->publicTechTags();
            if ($isCompact) {
                $techTags = array_slice($techTags, 0, 4);
            }
            $companyHeading = $exp->companyHeading($l);
            $companyLinkLabel = $l === 'es'
                ? 'Sitio web de '.$exp->companyName()
                : 'Website of '.$exp->companyName();
        @endphp

        <li class="timeline__item timeline__item--{{ $side }} @if($exp->is_current) is-current @endif" data-side="{{ $side }}">
            <span class="timeline__axis" aria-hidden="true">
                <span class="timeline__marker"></span>
                <span class="timeline__connector"></span>
            </span>

            <article class="timeline__card">
                <header class="timeline__header">
                    @if($period !== '' || $exp->is_current || $modality)
                        <div class="timeline__meta-row">
                            @if($period !== '')
                                <p class="timeline__period">
                                    <time
                                        @if($exp->startDateTimeAttr()) datetime="{{ $exp->startDateTimeAttr() }}" @endif
                                        @if($exp->endDateTimeAttr()) data-end="{{ $exp->endDateTimeAttr() }}" @endif
                                    >{{ $period }}</time>
                                </p>
                            @endif

                            @if($exp->is_current)
                                <span class="timeline__badge timeline__badge--current">{{ $currentLabel }}</span>
                            @elseif($modality)
                                <span class="timeline__badge">{{ $modality }}</span>
                            @endif
                        </div>
                    @endif

                    <h3 class="timeline__role">{{ $role }}</h3>

                    @if($companyHeading !== '')
                        <p class="timeline__company">
                            @if($exp->company_url)
                                <a href="{{ $exp->company_url }}"
                                   class="timeline__company-link"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   aria-label="{{ $companyLinkLabel }}">
                                    {{ $exp->companyName() }}
                                </a>
                            @else
                                <span>{{ $exp->companyName() }}</span>
                            @endif
                            @if($exp->sectorLabel($l))
                                <span class="timeline__sector"> · {{ $exp->sectorLabel($l) }}</span>
                            @endif
                            @if($location)
                                <span class="timeline__loc"> · {{ $location }}</span>
                            @endif
                        </p>
                    @endif
                </header>

                @if($paragraphs !== [])
                    <div class="timeline__desc">
                        @foreach($paragraphs as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                @endif

                @if($techTags !== [])
                    <ul class="timeline__tags" aria-label="{{ __('portfolio.sections.technologies') }}">
                        @foreach($techTags as $tag)
                            <li><span class="chip">{{ $tag }}</span></li>
                        @endforeach
                    </ul>
                @endif
            </article>
        </li>
    @endforeach
</ol>

@if($isCompact)
    <p class="timeline__more">
        <a href="{{ Locale::route('about') }}#experiencia" class="btn btn-ghost">
            {{ __('portfolio.experience.view_full') }}
        </a>
    </p>
@endif
