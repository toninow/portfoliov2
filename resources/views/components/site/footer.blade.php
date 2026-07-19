@php
    use App\Support\Locale;
    $locale = app()->getLocale();
    $adminAuthenticated = auth()->check();
    $adminLabel = $adminAuthenticated
        ? __('portfolio.footer.admin_logged_in')
        : __('portfolio.footer.admin');

    $priorityPlatforms = ['linkedin', 'github'];
    $prioritySocials = $socialLinks
        ->filter(fn ($s) => in_array(strtolower((string) $s->platform), $priorityPlatforms, true))
        ->sortBy(fn ($s) => array_search(strtolower((string) $s->platform), $priorityPlatforms, true));
    $otherSocials = $socialLinks->reject(fn ($s) => in_array(strtolower((string) $s->platform), $priorityPlatforms, true));
@endphp
<footer class="border-t border-[var(--color-line)] mt-16">
    <div class="container-page py-14 grid gap-10 md:grid-cols-[1.5fr_1fr_1fr]">
        <div>
            <div class="flex items-center gap-3">
                <span aria-hidden="true" class="grid place-items-center w-10 h-10 rounded-xl font-display font-bold text-white"
                      style="background: linear-gradient(135deg, var(--color-brand), var(--color-cyan));">AB</span>
                <span class="font-display font-semibold">{{ $siteProfile->name }}</span>
            </div>
            <p class="mt-4 max-w-sm text-sm text-[var(--color-muted)]">
                {{ $siteProfile->getTranslation('headline', $locale) ?: __('portfolio.footer.tagline') }}
            </p>
            @if($siteProfile->email)
                <a href="mailto:{{ $siteProfile->email }}" class="mt-3 inline-block text-sm text-[var(--color-brand-bright)] link-underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]">{{ $siteProfile->email }}</a>
            @endif
        </div>

        <nav aria-label="{{ __('portfolio.footer.nav_label') }}" class="text-sm">
            <ul class="space-y-2 text-[var(--color-muted)]">
                <li><a class="hover:text-[var(--color-ink)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]" href="{{ Locale::route('projects.index') }}">{{ __('portfolio.nav.projects') }}</a></li>
                <li><a class="hover:text-[var(--color-ink)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]" href="{{ Locale::route('services.index') }}">{{ __('portfolio.nav.services') }}</a></li>
                <li><a class="hover:text-[var(--color-ink)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]" href="{{ Locale::route('blog.index') }}">{{ __('portfolio.nav.blog') }}</a></li>
                <li><a class="hover:text-[var(--color-ink)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]" href="{{ Locale::route('about') }}">{{ __('portfolio.nav.about') }}</a></li>
                <li><a class="hover:text-[var(--color-ink)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]" href="{{ Locale::route('contact') }}">{{ __('portfolio.nav.contact') }}</a></li>
                @if($siteProfile->cvAvailable())
                    <li><a class="hover:text-[var(--color-ink)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]" href="{{ Locale::route('cv') }}">{{ __('portfolio.nav.cv') }}</a></li>
                @endif
            </ul>
        </nav>

        <div class="text-sm">
            @if($prioritySocials->isNotEmpty() || $otherSocials->isNotEmpty())
                <ul class="flex flex-wrap gap-3">
                    @foreach($prioritySocials as $social)
                        <li>
                            <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer"
                               class="chip hover:text-[var(--color-ink)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]">{{ $social->label ?: ucfirst($social->platform) }}</a>
                        </li>
                    @endforeach
                    @foreach($otherSocials as $social)
                        <li>
                            <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer"
                               class="chip hover:text-[var(--color-ink)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]">{{ $social->label ?: ucfirst($social->platform) }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
            <div class="mt-5 flex items-center gap-2 text-xs font-mono text-[var(--color-muted)]">
                <a href="{{ Locale::switchUrl('es') }}" @class(['px-2 py-1 rounded border border-[var(--color-line)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]', 'text-[var(--color-brand-bright)]' => $locale==='es'])>ES</a>
                <a href="{{ Locale::switchUrl('en') }}" @class(['px-2 py-1 rounded border border-[var(--color-line)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]', 'text-[var(--color-brand-bright)]' => $locale==='en'])>EN</a>
            </div>
        </div>
    </div>

    <div class="border-t border-[var(--color-line)]">
        <div class="container-page py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[var(--color-muted)]">
            <p>&copy; {{ date('Y') }} {{ $siteProfile->name }}</p>
            <nav aria-label="{{ __('portfolio.footer.legal_label') }}" class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2">
                <span class="font-mono hidden sm:inline">{{ __('portfolio.footer.built') }}</span>
                <a href="{{ url('/admin') }}"
                   class="inline-flex items-center gap-1.5 min-h-11 sm:min-h-0 py-2 sm:py-0 hover:text-[var(--color-ink)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-brand-bright)]"
                   rel="nofollow">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    {{ $adminLabel }}
                </a>
            </nav>
        </div>
    </div>
</footer>
