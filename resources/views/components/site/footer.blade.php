@php
    use App\Support\Locale;
    $locale = app()->getLocale();
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
                {{ $siteProfile->getTranslation('headline', $locale) }}
            </p>
            @if($siteProfile->email)
                <a href="mailto:{{ $siteProfile->email }}" class="mt-3 inline-block text-sm text-[var(--color-brand-bright)] link-underline">{{ $siteProfile->email }}</a>
            @endif
        </div>

        <nav aria-label="Footer" class="text-sm">
            <ul class="space-y-2 text-[var(--color-muted)]">
                <li><a class="hover:text-[var(--color-ink)]" href="{{ Locale::route('projects.index') }}">{{ __('portfolio.nav.projects') }}</a></li>
                <li><a class="hover:text-[var(--color-ink)]" href="{{ Locale::route('services.index') }}">{{ __('portfolio.nav.services') }}</a></li>
                <li><a class="hover:text-[var(--color-ink)]" href="{{ Locale::route('about') }}">{{ __('portfolio.nav.about') }}</a></li>
                <li><a class="hover:text-[var(--color-ink)]" href="{{ Locale::route('contact') }}">{{ __('portfolio.nav.contact') }}</a></li>
                <li><a class="hover:text-[var(--color-ink)]" href="{{ Locale::route('cv') }}">{{ __('portfolio.nav.cv') }}</a></li>
            </ul>
        </nav>

        <div class="text-sm">
            @if($socialLinks->isNotEmpty())
                <ul class="flex flex-wrap gap-3">
                    @foreach($socialLinks as $social)
                        <li>
                            <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer"
                               class="chip hover:text-[var(--color-ink)]">{{ $social->label ?: ucfirst($social->platform) }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
            <div class="mt-5 flex items-center gap-2 text-xs font-mono text-[var(--color-muted)]">
                <a href="{{ Locale::switchUrl('es') }}" @class(['px-2 py-1 rounded border border-[var(--color-line)]', 'text-[var(--color-brand-bright)]' => $locale==='es'])>ES</a>
                <a href="{{ Locale::switchUrl('en') }}" @class(['px-2 py-1 rounded border border-[var(--color-line)]', 'text-[var(--color-brand-bright)]' => $locale==='en'])>EN</a>
            </div>
        </div>
    </div>

    <div class="border-t border-[var(--color-line)]">
        <div class="container-page py-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[var(--color-muted)]">
            <p>&copy; {{ date('Y') }} {{ $siteProfile->name }}. {{ __('portfolio.footer.rights') }}</p>
            <p class="font-mono">{{ __('portfolio.footer.built') }}</p>
        </div>
    </div>
</footer>
