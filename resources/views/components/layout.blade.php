@props([
    'title' => '',
    'description' => null,
    'ogImage' => null,
    'canonical' => null,
    'indexable' => true,
    'jsonLd' => null,
    'absoluteTitle' => false,
])
@php
    use App\Support\Locale;
    use Illuminate\Support\Facades\Storage;
    $locale = app()->getLocale();
    $rawTitle = trim((string) $title);
    if ($rawTitle === '') {
        $pageTitle = $siteProfile->name;
    } elseif ($absoluteTitle || str_contains($rawTitle, $siteProfile->name)) {
        $pageTitle = $rawTitle;
    } else {
        $pageTitle = $rawTitle.' · '.$siteProfile->name;
    }
    $pageDescription = $description ?? $siteProfile->getTranslation('bio', $locale) ?? '';
    $resolvedOg = $ogImage ?? ($siteProfile->avatar_path ? Storage::url($siteProfile->avatar_path) : null);
    $jobTitle = $siteProfile->getTranslation('headline', $locale) ?: __('portfolio.hero.eyebrow');
    $defaultJsonLd = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Person',
                'name' => $siteProfile->name,
                'jobTitle' => $jobTitle,
                'url' => url('/'),
                'sameAs' => $socialLinks->pluck('url')->values()->all(),
            ],
            [
                '@type' => 'WebSite',
                'name' => $siteProfile->name,
                'url' => url('/'),
                'inLanguage' => [$locale === 'en' ? 'en' : 'es', $locale === 'en' ? 'es' : 'en'],
                'description' => $pageDescription,
            ],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <script>document.documentElement.classList.add('js');</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#07111f">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    <link rel="alternate" hreflang="es" href="{{ Locale::switchUrl('es') }}">
    <link rel="alternate" hreflang="en" href="{{ Locale::switchUrl('en') }}">
    <link rel="alternate" hreflang="x-default" href="{{ Locale::switchUrl('es') }}">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ $locale === 'en' ? 'en_GB' : 'es_ES' }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($resolvedOg)<meta property="og:image" content="{{ url($resolvedOg) }}">@endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    @if($resolvedOg)<meta name="twitter:image" content="{{ url($resolvedOg) }}">@endif

    @unless($indexable)
        <meta name="robots" content="noindex, nofollow">
    @endunless

    <link rel="icon" href="{{ Storage::url('profile/favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script type="application/ld+json">
    {!! json_encode($jsonLd ?? $defaultJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @stack('head')
</head>
<body class="min-h-screen antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:z-[60] focus:top-3 focus:left-3 btn btn-primary">
        {{ __('portfolio.nav.skip') }}
    </a>

    <x-site.nav />

    <main id="main" data-page>
        {{ $slot }}
    </main>

    <x-site.footer />
</body>
</html>
