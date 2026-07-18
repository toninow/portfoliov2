@php
    $l = app()->getLocale();

    // Each node = an element Antonio connects. Descriptions are real, high-level and
    // client-safe (no credentials, hosts or confidential data).
    $nodes = [
        [
            'key' => 'web', 'glyph' => '⬡',
            'label' => ['es' => 'Web & landings', 'en' => 'Web & landings'],
            'role' => ['es' => 'Presencia online', 'en' => 'Online presence'],
            'desc' => ['es' => 'Sitios web, landings y plataformas para instituciones y empresas: WordPress, Bitrix y desarrollos a medida con Laravel.', 'en' => 'Websites, landings and platforms for institutions and companies: WordPress, Bitrix and custom Laravel builds.'],
            'tags' => ['WordPress', 'Laravel', 'Bitrix', 'SEO'],
        ],
        [
            'key' => 'data', 'glyph' => '⇢',
            'label' => ['es' => 'Datos & proveedores', 'en' => 'Data & suppliers'],
            'role' => ['es' => 'Entrada y normalización', 'en' => 'Intake & normalization'],
            'desc' => ['es' => 'Catálogos de proveedores en Excel y web: SKU, EAN, precios e imágenes, limpiados y validados antes de entrar al sistema.', 'en' => 'Supplier catalogs from Excel and web: SKU, EAN, prices and images, cleaned and validated before entering the system.'],
            'tags' => ['Excel', 'CSV', 'EAN', 'Validación'],
        ],
        [
            'key' => 'ai', 'glyph' => '✦',
            'label' => ['es' => 'Automatización + IA', 'en' => 'Automation + AI'],
            'role' => ['es' => 'Motor asistido por IA', 'en' => 'AI-assisted engine'],
            'desc' => ['es' => 'Automatizo tareas repetitivas y uso IA (asistentes como GPT/Claude y editores como Cursor) para acelerar el desarrollo, generar y revisar código y procesar datos con mejor criterio.', 'en' => 'I automate repetitive tasks and use AI (assistants like GPT/Claude and editors like Cursor) to speed up development, generate and review code, and process data with better judgement.'],
            'tags' => ['IA', 'GPT / Claude', 'Cursor', 'Scripts'],
        ],
        [
            'key' => 'erp', 'glyph' => '▤',
            'label' => ['es' => 'ERP · Dolibarr', 'en' => 'ERP · Dolibarr'],
            'role' => ['es' => 'Núcleo de negocio', 'en' => 'Business core'],
            'desc' => ['es' => 'Productos, precios y stock centralizados en Dolibarr, con módulos a medida.', 'en' => 'Products, prices and stock centralized in Dolibarr, with custom modules.'],
            'tags' => ['Dolibarr', 'PHP', 'MySQL', 'Módulos'],
        ],
        [
            'key' => 'ecommerce', 'glyph' => '▣',
            'label' => ['es' => 'PrestaShop', 'en' => 'PrestaShop'],
            'role' => ['es' => 'Tienda online', 'en' => 'Online store'],
            'desc' => ['es' => 'Sincronización de productos, precios y stock del ERP hacia el ecommerce, con diagnóstico de errores.', 'en' => 'Sync of products, prices and stock from the ERP to the storefront, with error diagnostics.'],
            'tags' => ['PrestaShop', 'API', 'Sync'],
        ],
        [
            'key' => 'stock', 'glyph' => '▦',
            'label' => ['es' => 'Inventario', 'en' => 'Inventory'],
            'role' => ['es' => 'Stock en tiempo real', 'en' => 'Real-time stock'],
            'desc' => ['es' => 'Consulta de existencias por almacén, EAN, UPC y referencia desde el móvil.', 'en' => 'Stock lookups by warehouse, EAN, UPC and reference from mobile.'],
            'tags' => ['API', 'Flutter', 'EAN'],
        ],
        [
            'key' => 'apps', 'glyph' => '◈',
            'label' => ['es' => 'Apps a medida', 'en' => 'Custom apps'],
            'role' => ['es' => 'Herramientas internas', 'en' => 'Internal tooling'],
            'desc' => ['es' => 'Aplicaciones web y móviles para el trabajo diario: Laravel, Livewire y Flutter, con foco en una UX real y usable.', 'en' => 'Web and mobile apps for daily work: Laravel, Livewire and Flutter, focused on real, usable UX.'],
            'tags' => ['Laravel', 'Livewire', 'Flutter'],
        ],
        [
            'key' => 'db', 'glyph' => '⧉',
            'label' => ['es' => 'APIs & bases de datos', 'en' => 'APIs & databases'],
            'role' => ['es' => 'Datos & conexiones', 'en' => 'Data & connections'],
            'desc' => ['es' => 'Modelado y consultas en MySQL y PostgreSQL, e integraciones vía APIs REST para conectar sistemas que antes no se hablaban.', 'en' => 'Data modeling and queries in MySQL and PostgreSQL, plus REST API integrations to connect systems that did not talk to each other.'],
            'tags' => ['MySQL', 'PostgreSQL', 'REST'],
        ],
        [
            'key' => 'infra', 'glyph' => '❖',
            'label' => ['es' => 'Infraestructura', 'en' => 'Infrastructure'],
            'role' => ['es' => 'Servidores & backups', 'en' => 'Servers & backups'],
            'desc' => ['es' => 'Linux y Docker, Gitea autogestionado, HTTPS, despliegues y copias verificables con Restic.', 'en' => 'Linux and Docker, self-hosted Gitea, HTTPS, deploys and verifiable backups with Restic.'],
            'tags' => ['Linux', 'Docker', 'Gitea', 'Restic'],
        ],
    ];

    // Positions on a 440 x 380 stage. AI/automation sits in the middle as the
    // engine that touches everything — the work is a mesh, not a single pipeline.
    $pos = [
        [72, 60],    // 0 web
        [206, 46],   // 1 data
        [220, 190],  // 2 ai (hub)
        [368, 72],   // 3 erp
        [398, 198],  // 4 ecommerce
        [332, 316],  // 5 stock
        [178, 344],  // 6 apps
        [64, 306],   // 7 db
        [38, 168],   // 8 infra
    ];
    // Interconnected flow (a mesh centred on automation + AI).
    $edges = [
        [1, 2], [0, 2], [2, 3], [2, 6], [2, 4],
        [3, 4], [3, 5], [4, 5], [6, 5], [6, 7],
        [7, 3], [8, 7], [8, 0],
    ];

    // Adjacency for JS highlight.
    $adj = [];
    foreach ($edges as $e) {
        $adj[$e[0]][] = $e[1];
        $adj[$e[1]][] = $e[0];
    }
@endphp

<figure class="system-map" data-system-map role="group" aria-labelledby="system-map-title">
    <figcaption id="system-map-title" class="sr-only">{{ __('portfolio.hero.map_desc') }}</figcaption>

    {{-- Desktop / tablet: interactive SVG minimap --}}
    <div class="hidden sm:block system-map__frame surface">
        <div class="system-map__bar">
            <span class="eyebrow">{{ __('portfolio.hero.map_title') }}</span>
            <span class="system-map__hint" data-map-hint>{{ __('portfolio.hero.map_hint') }}</span>
            <span class="system-map__live"><span class="system-map__live-dot"></span>live</span>
        </div>

        <div class="system-map__stage">
            <svg viewBox="0 0 440 380" class="system-map__svg" role="img"
                 aria-label="{{ __('portfolio.hero.map_desc') }}">
                <defs>
                    <linearGradient id="edge" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#3b82f6"/>
                        <stop offset="100%" stop-color="#22d3ee"/>
                    </linearGradient>
                    <radialGradient id="nodeCore" cx="50%" cy="45%" r="60%">
                        <stop offset="0%" stop-color="#60a5fa"/>
                        <stop offset="100%" stop-color="#0d1b2a"/>
                    </radialGradient>
                </defs>

                {{-- Edges --}}
                <g class="system-map__edges">
                    @foreach($edges as $i => [$a, $b])
                        @php $d = "M {$pos[$a][0]} {$pos[$a][1]} L {$pos[$b][0]} {$pos[$b][1]}"; @endphp
                        <path class="map-edge" data-edge="{{ $a }}-{{ $b }}" d="{{ $d }}"
                              stroke="url(#edge)" fill="none" pathLength="1"/>
                    @endforeach
                </g>

                {{-- Data pulses travelling along a few edges --}}
                <g class="system-map__pulses" aria-hidden="true">
                    @foreach([[1,2],[2,3],[3,4],[2,6],[8,7]] as $k => [$a, $b])
                        @php $d = "M {$pos[$a][0]} {$pos[$a][1]} L {$pos[$b][0]} {$pos[$b][1]}"; @endphp
                        <circle r="2.6" fill="#22d3ee" class="map-pulse">
                            <animateMotion dur="{{ 2.4 + $k * 0.5 }}s" repeatCount="indefinite"
                                           begin="{{ $k * 0.6 }}s" path="{{ $d }}"/>
                        </circle>
                    @endforeach
                </g>

                {{-- Nodes --}}
                <g class="system-map__nodes">
                    @foreach($nodes as $i => $node)
                        <g class="map-node" data-node="{{ $i }}" data-key="{{ $node['key'] }}"
                           data-adj="{{ implode(',', $adj[$i] ?? []) }}"
                           data-label="{{ $node['label'][$l] }}"
                           data-role="{{ $node['role'][$l] }}"
                           data-desc="{{ $node['desc'][$l] }}"
                           data-tags="{{ implode('|', $node['tags']) }}"
                           tabindex="0" role="button"
                           aria-label="{{ $node['label'][$l] }} — {{ $node['role'][$l] }}">
                            <title>{{ $node['label'][$l] }} — {{ $node['desc'][$l] }}</title>
                            <circle class="map-node__halo" cx="{{ $pos[$i][0] }}" cy="{{ $pos[$i][1] }}" r="20"/>
                            <circle class="map-node__ring" cx="{{ $pos[$i][0] }}" cy="{{ $pos[$i][1] }}" r="12"
                                    fill="url(#nodeCore)" stroke="#60a5fa" stroke-width="2"/>
                            <text class="map-node__glyph" x="{{ $pos[$i][0] }}" y="{{ $pos[$i][1] + 4 }}"
                                  text-anchor="middle" font-size="12" fill="#e0f2fe">{{ $node['glyph'] }}</text>
                            @php
                                $x = $pos[$i][0];
                                $anchor = $x < 150 ? 'start' : ($x > 300 ? 'end' : 'middle');
                                $ty = $pos[$i][1] < 70 ? $pos[$i][1] - 22 : $pos[$i][1] + 30;
                            @endphp
                            <text class="map-node__label" x="{{ $x }}" y="{{ $ty }}"
                                  text-anchor="{{ $anchor }}">{{ $node['label'][$l] }}</text>
                        </g>
                    @endforeach
                </g>

                {{-- HUD corner brackets --}}
                <g class="system-map__hud" aria-hidden="true" stroke="#22d3ee" stroke-width="1.5" fill="none" opacity="0.5">
                    <path d="M10 26 V10 H26"/><path d="M414 10 H430 V26"/>
                    <path d="M430 354 V370 H414"/><path d="M26 370 H10 V354"/>
                </g>
            </svg>

            {{-- Detail panel (updated by JS on select) --}}
            <div class="system-map__panel" data-map-panel aria-live="polite">
                <p class="system-map__panel-empty" data-map-empty>{{ __('portfolio.hero.map_empty') }}</p>
                <div class="system-map__panel-body" data-map-body hidden>
                    <span class="system-map__panel-role" data-map-role></span>
                    <h3 class="system-map__panel-title" data-map-title></h3>
                    <p class="system-map__panel-desc" data-map-desc></p>
                    <div class="system-map__panel-tags" data-map-tags></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile: accessible vertical flow with expandable detail (no JS needed) --}}
    <ol class="sm:hidden system-map__mobile">
        @foreach($nodes as $i => $node)
            <li class="system-map__mstep">
                <span aria-hidden="true" class="system-map__mdot"></span>
                @unless($loop->last)<span aria-hidden="true" class="system-map__mline"></span>@endunless
                <details class="card system-map__mcard" @if($loop->first) open @endif>
                    <summary>
                        <span class="system-map__mglyph" aria-hidden="true">{{ $node['glyph'] }}</span>
                        <span>
                            <span class="system-map__mlabel">{{ $node['label'][$l] }}</span>
                            <span class="system-map__mrole">{{ $node['role'][$l] }}</span>
                        </span>
                    </summary>
                    <p class="system-map__mdesc">{{ $node['desc'][$l] }}</p>
                    <div class="system-map__panel-tags">
                        @foreach($node['tags'] as $t)<span class="chip">{{ $t }}</span>@endforeach
                    </div>
                </details>
            </li>
        @endforeach
    </ol>
</figure>
