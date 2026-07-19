@props(['experiences'])

@php $l = app()->getLocale(); @endphp

<ol class="timeline" data-reveal-stagger>
    @foreach($experiences as $exp)
        @php
            $role = $exp->getTranslation('role', $l);
            $description = $exp->getTranslation('description', $l);
            $paragraphs = filled($description)
                ? preg_split("/\n\s*\n/", trim($description)) ?: []
                : [];
            $location = $exp->displayLocation();
            $modality = $exp->modalityLabel($l);
            $period = $exp->periodLabel($l);
            $achievements = $exp->publicAchievements($l);
            $techTags = $exp->publicTechTags();
            $companyLabel = $exp->company;
            $companyLinkLabel = $l === 'es'
                ? 'Sitio web de '.$companyLabel
                : 'Website of '.$companyLabel;
        @endphp

        <li class="timeline__item @if($exp->is_current) is-current @endif">
            <span class="timeline__marker" aria-hidden="true"></span>
            <article class="timeline__card">
                @if($period !== '')
                    <p class="timeline__period">
                        <time
                            @if($exp->startDateTimeAttr()) datetime="{{ $exp->startDateTimeAttr() }}" @endif
                        >{{ $period }}</time>
                        @if($exp->is_current)
                            <span class="sr-only">
                                {{ $l === 'es' ? 'Puesto actual' : 'Current role' }}
                            </span>
                        @endif
                    </p>
                @endif

                <h3 class="timeline__role">{{ $role }}</h3>

                @if($companyLabel)
                    <p class="timeline__company">
                        @if($exp->company_url)
                            <a href="{{ $exp->company_url }}"
                               class="timeline__company-link"
                               target="_blank"
                               rel="noopener noreferrer"
                               aria-label="{{ $companyLinkLabel }}">
                                {{ $companyLabel }}
                            </a>
                        @else
                            <span>{{ $companyLabel }}</span>
                        @endif
                        @if($location)
                            <span class="timeline__loc"> · {{ $location }}</span>
                        @endif
                    </p>
                @endif

                @if($modality)
                    <p class="timeline__meta">
                        <span class="timeline__badge">{{ $modality }}</span>
                    </p>
                @endif

                @if($paragraphs !== [])
                    <div class="timeline__desc">
                        @foreach($paragraphs as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                @endif

                @if($achievements !== [])
                    <ul class="timeline__achievements">
                        @foreach($achievements as $achievement)
                            <li>
                                <strong>{{ $achievement['title'] }}</strong>
                                @if(!empty($achievement['metric']))
                                    <span class="timeline__metric">{{ $achievement['metric'] }}</span>
                                @endif
                                @if(!empty($achievement['description']))
                                    <span>{{ $achievement['description'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if($techTags !== [])
                    <ul class="timeline__tags" aria-label="{{ __('portfolio.sections.technologies') }}">
                        @foreach($techTags as $tag)
                            <li class="chip">{{ $tag }}</li>
                        @endforeach
                    </ul>
                @endif
            </article>
        </li>
    @endforeach
</ol>
