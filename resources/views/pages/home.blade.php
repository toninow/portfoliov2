@php
    use App\Support\Locale;
    use Illuminate\Support\Facades\Storage;
    $l = app()->getLocale();
    $availability = $profile->getTranslation('availability', $l) ?: __('portfolio.availability.available');
    $capabilities = [
        ['t' => ['es' => 'Sistemas internos y apps a medida', 'en' => 'Internal systems & custom apps'], 'd' => ['es' => 'Aplicaciones web y móviles (Laravel, Livewire, Flutter) para el trabajo diario del equipo.', 'en' => 'Web and mobile apps (Laravel, Livewire, Flutter) for the team\'s daily work.']],
        ['t' => ['es' => 'Desarrollo web & landings', 'en' => 'Web development & landings'], 'd' => ['es' => 'Sitios, plataformas y landings para instituciones y empresas, con foco en velocidad y SEO.', 'en' => 'Sites, platforms and landings for institutions and companies, focused on speed and SEO.']],
        ['t' => ['es' => 'Integraciones ERP · ecommerce', 'en' => 'ERP · ecommerce integrations'], 'd' => ['es' => 'Sincronizo productos, precios y stock entre Dolibarr, PrestaShop y otros sistemas vía API.', 'en' => 'I sync products, prices and stock between Dolibarr, PrestaShop and other systems via API.']],
        ['t' => ['es' => 'Automatización con IA', 'en' => 'AI-assisted automation'], 'd' => ['es' => 'Elimino trabajo manual y uso IA para generar, revisar código y procesar datos más rápido.', 'en' => 'I remove manual work and use AI to generate, review code and process data faster.']],
        ['t' => ['es' => 'Datos, APIs y bases de datos', 'en' => 'Data, APIs & databases'], 'd' => ['es' => 'Modelado y consultas en MySQL/PostgreSQL e integraciones REST entre sistemas.', 'en' => 'Data modeling and queries in MySQL/PostgreSQL and REST integrations between systems.']],
        ['t' => ['es' => 'Infraestructura, Git & backups', 'en' => 'Infrastructure, Git & backups'], 'd' => ['es' => 'Linux, Docker, Gitea autogestionado, HTTPS y copias verificables con Restic.', 'en' => 'Linux, Docker, self-hosted Gitea, HTTPS and verifiable backups with Restic.']],
    ];
    $process = [
        'understand' => ['es' => 'Escucho el proceso real y a las personas que lo usan.', 'en' => 'I listen to the real process and the people using it.'],
        'audit' => ['es' => 'Reviso datos, sistemas y puntos de fricción.', 'en' => 'I review data, systems and friction points.'],
        'design' => ['es' => 'Defino una solución clara y mantenible.', 'en' => 'I define a clear, maintainable solution.'],
        'implement' => ['es' => 'Construyo, integro y pruebo con datos reales.', 'en' => 'I build, integrate and test with real data.'],
        'measure' => ['es' => 'Mido resultados y mejoro de forma continua.', 'en' => 'I measure results and improve continuously.'],
    ];
    $areaLabels = ['backend' => 'Backend', 'frontend' => 'Frontend & móvil', 'data' => 'Datos & APIs', 'erp' => 'ERP & ecommerce', 'infra' => 'Infraestructura', 'ia' => 'IA & productividad', 'tools' => 'Herramientas'];
@endphp

<x-layout :description="$profile->getTranslation('headline', $l)">

    {{-- 1 · Hero --}}
    <section class="relative overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -top-40 -left-40 w-[36rem] h-[36rem] rounded-full opacity-30 blur-3xl" style="background: radial-gradient(circle, rgba(59,130,246,.5), transparent 60%);"></div>
            <div class="absolute -bottom-40 -right-40 w-[32rem] h-[32rem] rounded-full opacity-20 blur-3xl" style="background: radial-gradient(circle, rgba(34,211,238,.5), transparent 60%);"></div>
        </div>

        <div class="container-page section grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div data-hero>
                <p class="eyebrow">{{ __('portfolio.hero.eyebrow') }}</p>
                <h1 class="mt-4 text-4xl sm:text-5xl lg:text-[3.4rem] leading-[1.05] font-bold text-balance">
                    {{ __('portfolio.hero.title') }}
                </h1>
                <p class="mt-3 text-base font-mono text-[var(--color-brand-bright)]">
                    {{ $profile->getTranslation('headline', $l) }}
                </p>
                <p class="mt-5 text-lg text-[var(--color-muted)] max-w-xl">
                    {{ $profile->getTranslation('bio', $l) ?: __('portfolio.hero.lead') }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ Locale::route('projects.index') }}" class="btn btn-primary">{{ __('portfolio.hero.view_projects') }}</a>
                    <a href="{{ Locale::route('contact') }}" class="btn btn-ghost">{{ __('portfolio.hero.talk') }}</a>
                    @if($profile->cvAvailable())
                        <a href="{{ Locale::route('cv') }}" class="btn btn-ghost" target="_blank" rel="noopener">{{ __('portfolio.hero.download_cv') }}</a>
                    @endif
                </div>

                @if($availability)
                    <p class="mt-6 inline-flex items-center gap-2 text-sm text-[var(--color-muted)]">
                        <span class="relative flex w-2.5 h-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-60" style="background: var(--color-positive);"></span>
                            <span class="relative inline-flex rounded-full w-2.5 h-2.5" style="background: var(--color-positive);"></span>
                        </span>
                        {{ $availability }}
                    </p>
                @endif
            </div>

            <div>
                <x-site.system-map />
            </div>
        </div>
    </section>

    {{-- 2 · Specialization band --}}
    <section class="border-y border-[var(--color-line)] bg-[var(--color-surface)]/40">
        <div class="container-page py-8 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-sm font-mono text-[var(--color-muted)]" data-reveal-stagger>
            @foreach(['Desarrollo web & apps', 'ERP & ecommerce', 'Automatización + IA', 'Integraciones & APIs', 'Inventario', 'Infraestructura'] as $item)
                <span class="flex items-center gap-2"><span aria-hidden="true" style="color: var(--color-cyan)">◇</span>{{ $item }}</span>
            @endforeach
        </div>
    </section>

    {{-- 4 · Featured projects --}}
    @if($featuredLarge->isNotEmpty() || $featuredMedium->isNotEmpty() || $featuredCompact->isNotEmpty())
    <section class="section">
        <div class="container-page">
            <div class="flex items-end justify-between gap-4 mb-10">
                <div>
                    <p class="eyebrow">{{ __('portfolio.sections.featured') }}</p>
                    <h2 class="mt-2 text-3xl sm:text-4xl font-bold">{{ __('portfolio.sections.featured') }}</h2>
                </div>
                <a href="{{ Locale::route('projects.index') }}" class="hidden sm:inline-flex btn btn-ghost">{{ __('portfolio.projects.all') }}</a>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                @foreach($featuredLarge as $project)
                    <x-site.project-card :project="$project" size="large" />
                @endforeach
                @foreach($featuredMedium as $project)
                    <x-site.project-card :project="$project" size="medium" />
                @endforeach
            </div>

            @if($featuredCompact->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mt-5">
                    @foreach($featuredCompact as $project)
                        <x-site.project-card :project="$project" size="compact" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    @endif

    {{-- 5 · Capabilities (bento) --}}
    <section class="section bg-[var(--color-surface)]/40 border-y border-[var(--color-line)]">
        <div class="container-page">
            <p class="eyebrow">{{ __('portfolio.sections.capabilities') }}</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-bold mb-10">{{ __('portfolio.sections.capabilities') }}</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($capabilities as $cap)
                    <div data-reveal class="card p-6">
                        <h3 class="font-display font-semibold text-lg">{{ $cap['t'][$l] }}</h3>
                        <p class="mt-2 text-sm text-[var(--color-muted)]">{{ $cap['d'][$l] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 6 · Process --}}
    <section class="section">
        <div class="container-page">
            <p class="eyebrow">{{ __('portfolio.sections.process') }}</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-bold mb-10">{{ __('portfolio.sections.process') }}</h2>
            <ol class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($process as $key => $desc)
                    <li data-reveal class="card p-5">
                        <span class="font-mono text-xs text-[var(--color-brand-bright)]">0{{ $loop->iteration }}</span>
                        <h3 class="mt-2 font-display font-semibold">{{ __('portfolio.process.'.$key) }}</h3>
                        <p class="mt-2 text-sm text-[var(--color-muted)]">{{ $desc[$l] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- 7 · Experience --}}
    @if($experiences->isNotEmpty())
    <section class="section bg-[var(--color-surface)]/40 border-y border-[var(--color-line)]">
        <div class="container-page">
            <p class="eyebrow">{{ __('portfolio.sections.experience') }}</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-bold mb-10">{{ __('portfolio.sections.experience') }}</h2>
            <div class="space-y-4">
                @foreach($experiences as $exp)
                    <div data-reveal class="card p-5 sm:flex items-start gap-6">
                        <div class="font-mono text-sm text-[var(--color-muted)] sm:w-40 shrink-0">
                            {{ $exp->start_date }}@if($exp->start_date) – @endif{{ $exp->is_current ? ($l==='es'?'Actualidad':'Present') : $exp->end_date }}
                        </div>
                        <div>
                            <h3 class="font-display font-semibold">{{ $exp->getTranslation('role', $l) }}@if($exp->company) · <span class="text-[var(--color-muted)]">{{ $exp->company }}</span>@endif</h3>
                            @if($exp->getTranslation('description', $l))
                                <p class="mt-1 text-sm text-[var(--color-muted)]">{{ $exp->getTranslation('description', $l) }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 8 · Technologies --}}
    @if($technologies->isNotEmpty())
    <section class="section">
        <div class="container-page">
            <p class="eyebrow">{{ __('portfolio.sections.technologies') }}</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-bold mb-10">{{ __('portfolio.sections.technologies') }}</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($technologies as $area => $techs)
                    <div class="card p-6">
                        <h3 class="font-mono text-xs uppercase tracking-wider text-[var(--color-brand-bright)]">{{ $areaLabels[$area] ?? $area }}</h3>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($techs as $tech)
                                <span class="chip">{{ $tech->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 9 · About --}}
    <section class="section bg-[var(--color-surface)]/40 border-y border-[var(--color-line)]">
        <div class="container-page grid lg:grid-cols-[1fr_1.4fr] gap-10 items-center">
            @if($profile->avatar_path)
                <div data-reveal class="max-w-xs mx-auto lg:mx-0">
                    <img src="{{ Storage::url($profile->avatar_path) }}" alt="{{ $profile->name }}"
                         class="w-full rounded-2xl border border-[var(--color-line)]" loading="lazy">
                </div>
            @endif
            <div>
                <p class="eyebrow">{{ __('portfolio.sections.about') }}</p>
                <h2 class="mt-2 text-3xl sm:text-4xl font-bold">{{ $profile->name }}</h2>
                <p class="mt-5 text-lg text-[var(--color-muted)]">{{ $profile->getTranslation('about_long', $l) ?: $profile->getTranslation('bio', $l) }}</p>
                <a href="{{ Locale::route('about') }}" class="mt-6 inline-flex btn btn-ghost">{{ __('portfolio.nav.about') }}</a>
            </div>
        </div>
    </section>

    {{-- 10 + 11 · CTA + quick contact --}}
    <section class="section">
        <div class="container-page">
            <div class="surface p-8 sm:p-12 text-center" style="box-shadow: var(--shadow-glow);">
                <h2 class="text-2xl sm:text-3xl font-bold max-w-3xl mx-auto text-balance">{{ __('portfolio.cta.title') }}</h2>
                <p class="mt-4 text-[var(--color-muted)] max-w-2xl mx-auto">{{ __('portfolio.cta.body') }}</p>
                <a href="{{ Locale::route('contact') }}" class="mt-8 inline-flex btn btn-primary">{{ __('portfolio.cta.button') }}</a>
            </div>
        </div>
    </section>

</x-layout>
