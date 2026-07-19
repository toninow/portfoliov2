@props(['experiences'])

@php $l = app()->getLocale(); @endphp

<ol class="timeline" data-reveal-stagger>
    @foreach($experiences as $exp)
        <li class="timeline__item @if($exp->is_current) is-current @endif">
            <span class="timeline__marker" aria-hidden="true"></span>
            <div class="timeline__card">
                <div class="timeline__period">
                    <span>{{ $exp->start_date }}@if($exp->start_date && ($exp->end_date || $exp->is_current)) – @endif{{ $exp->is_current ? ($l === 'es' ? 'Actualidad' : 'Present') : $exp->end_date }}</span>
                    @if($exp->is_current)
                        <span class="timeline__now">{{ $l === 'es' ? 'Actual' : 'Now' }}</span>
                    @endif
                </div>
                <h3 class="timeline__role">{{ $exp->getTranslation('role', $l) }}</h3>
                @if($exp->company)
                    <p class="timeline__company">
                        @if($exp->company_url)
                            <a href="{{ $exp->company_url }}" class="timeline__company-link" target="_blank" rel="noopener noreferrer">{{ $exp->company }}</a>
                        @else
                            {{ $exp->company }}
                        @endif
                        @if($exp->location) <span class="timeline__loc">· {{ $exp->location }}</span>@endif
                    </p>
                @endif
                @if($exp->getTranslation('description', $l))
                    <p class="timeline__desc">{{ $exp->getTranslation('description', $l) }}</p>
                @endif
            </div>
        </li>
    @endforeach
</ol>
