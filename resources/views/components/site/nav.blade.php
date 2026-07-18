@php
    use App\Support\Locale;
    $locale = app()->getLocale();
    $links = [
        ['label' => __('portfolio.nav.home'), 'url' => Locale::route('home'), 'name' => 'home'],
        ['label' => __('portfolio.nav.projects'), 'url' => Locale::route('projects.index'), 'name' => 'projects.index'],
        ['label' => __('portfolio.nav.services'), 'url' => Locale::route('services.index'), 'name' => 'services.index'],
        ['label' => __('portfolio.nav.blog'), 'url' => Locale::route('blog.index'), 'name' => 'blog.index'],
        ['label' => __('portfolio.nav.about'), 'url' => Locale::route('about'), 'name' => 'about'],
        ['label' => __('portfolio.nav.contact'), 'url' => Locale::route('contact'), 'name' => 'contact'],
    ];
    $current = preg_replace('/^en\./', '', request()->route()?->getName() ?? '');
@endphp

<header data-sticky-nav class="fixed inset-x-0 top-0 z-50">
    <nav class="container-page flex items-center justify-between h-16 md:h-20" aria-label="Principal">
        <a href="{{ Locale::route('home') }}" class="flex items-center gap-3 shrink-0">
            <span aria-hidden="true"
                  class="grid place-items-center w-10 h-10 rounded-xl font-display font-bold text-white"
                  style="background: linear-gradient(135deg, var(--color-brand), var(--color-cyan));">AB</span>
            <span class="hidden sm:block font-display font-semibold tracking-tight">{{ $siteProfile->name }}</span>
        </a>

        <ul class="hidden lg:flex items-center gap-8 text-sm text-[var(--color-muted)]">
            @foreach($links as $link)
                <li>
                    <a href="{{ $link['url'] }}"
                       @class(['link-underline transition-colors hover:text-[var(--color-ink)]', 'text-[var(--color-ink)]' => $current === $link['name']])
                       @if($current === $link['name']) aria-current="page" @endif>
                        {{ $link['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="flex items-center gap-2 sm:gap-3">
            <div class="hidden sm:flex items-center rounded-lg border border-[var(--color-line)] overflow-hidden text-xs font-mono">
                <a href="{{ Locale::switchUrl('es') }}"
                   @class(['px-2.5 py-1.5', 'bg-[var(--color-brand)] text-white' => $locale === 'es', 'text-[var(--color-muted)]' => $locale !== 'es'])
                   aria-label="Español" @if($locale==='es') aria-current="true" @endif>ES</a>
                <a href="{{ Locale::switchUrl('en') }}"
                   @class(['px-2.5 py-1.5', 'bg-[var(--color-brand)] text-white' => $locale === 'en', 'text-[var(--color-muted)]' => $locale !== 'en'])
                   aria-label="English" @if($locale==='en') aria-current="true" @endif>EN</a>
            </div>

            <a href="{{ Locale::route('contact') }}" class="hidden sm:inline-flex btn btn-primary">
                {{ __('portfolio.nav.cta') }}
            </a>

            <button type="button" data-nav-toggle aria-expanded="false" aria-controls="mobile-menu"
                    class="lg:hidden grid place-items-center w-11 h-11 rounded-lg border border-[var(--color-line)] text-[var(--color-ink)]">
                <span class="sr-only">{{ __('portfolio.nav.menu') }}</span>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M3 6h18M3 12h18M3 18h18"/>
                </svg>
            </button>
        </div>
    </nav>

    <div id="mobile-menu" hidden class="lg:hidden border-t border-[var(--color-line)] bg-[var(--color-surface)]">
        <div class="container-page py-4 flex flex-col gap-1">
            @foreach($links as $link)
                <a href="{{ $link['url'] }}" class="py-3 text-base text-[var(--color-muted)] hover:text-[var(--color-ink)]">{{ $link['label'] }}</a>
            @endforeach
            <a href="{{ Locale::route('contact') }}" class="btn btn-primary w-full mt-3">{{ __('portfolio.nav.cta') }}</a>
            <div class="flex items-center gap-3 mt-3 text-xs font-mono">
                <a href="{{ Locale::switchUrl('es') }}" @class(['px-3 py-2 rounded-lg border border-[var(--color-line)]', 'text-[var(--color-brand-bright)]' => $locale==='es'])>ES</a>
                <a href="{{ Locale::switchUrl('en') }}" @class(['px-3 py-2 rounded-lg border border-[var(--color-line)]', 'text-[var(--color-brand-bright)]' => $locale==='en'])>EN</a>
            </div>
        </div>
    </div>
</header>
<div aria-hidden="true" class="h-16 md:h-20"></div>
