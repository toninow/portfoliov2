<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SkillGroup;
use App\Models\SocialLink;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds the portfolio with real content migrated from the legacy static
 * site (legacy-portfolio/). No invented metrics or credentials are added:
 * fields without a confirmed public value are left empty and editable.
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedProfile();
        $this->seedSocials();
        $technologies = $this->seedTechnologies();
        $this->seedSkills();
        $this->seedServices($technologies);
        $categories = $this->seedCategories();
        $this->seedFeaturedProjects($categories, $technologies);
        $this->seedLegacyProjects($categories, $technologies);
        $this->seedExperience();
        $this->seedEducation();
    }

    protected function seedProfile(): void
    {
        Profile::updateOrCreate(['id' => 1], [
            'name' => 'Antonio Benalcázar',
            'headline' => [
                'es' => 'Desarrollador de software full-stack · Automatización e integraciones',
                'en' => 'Full-stack software developer · Automation and integrations',
            ],
            'bio' => [
                'es' => 'Desarrollo aplicaciones web e internas, automatizo procesos e integro sistemas (ERP, ecommerce, inventario, APIs e infraestructura). Me apoyo en la IA para construir más rápido y con mejor criterio.',
                'en' => 'I build web and internal applications, automate processes and integrate systems (ERP, ecommerce, inventory, APIs and infrastructure). I use AI to build faster and with better judgement.',
            ],
            'about_long' => [
                'es' => "Soy desarrollador de software full-stack. Empecé construyendo sitios web, plataformas y landings para instituciones y empresas, y con el tiempo me especialicé en resolver problemas de negocio concretos: sistemas internos, automatización de procesos e integraciones entre ERP (Dolibarr), ecommerce (PrestaShop), inventario, catálogos de proveedores e infraestructura.\n\nTrabajo de punta a punta: backend con PHP y Laravel, interfaces web y móviles (Livewire, Flutter), bases de datos MySQL y PostgreSQL, APIs REST y servidores Linux con Docker, Git y copias de seguridad.\n\nUso la inteligencia artificial como parte natural de mi flujo de trabajo —asistentes como GPT y Claude y editores como Cursor— para generar y revisar código, procesar datos y avanzar más rápido sin perder criterio. Me interesan los procesos que todavía dependen de hojas de cálculo, mensajes y trabajo manual, y disfruto convirtiéndolos en sistemas más claros, medibles y fáciles de mantener.",
                'en' => "I am a full-stack software developer. I started building websites, platforms and landing pages for institutions and companies, and over time I specialised in solving concrete business problems: internal systems, process automation and integrations between ERP (Dolibarr), ecommerce (PrestaShop), inventory, supplier catalogs and infrastructure.\n\nI work end to end: backend with PHP and Laravel, web and mobile interfaces (Livewire, Flutter), MySQL and PostgreSQL databases, REST APIs and Linux servers with Docker, Git and backups.\n\nI use artificial intelligence as a natural part of my workflow —assistants like GPT and Claude and editors like Cursor— to generate and review code, process data and move faster without losing judgement. I am drawn to processes that still rely on spreadsheets, messages and manual work, and I enjoy turning them into clearer, measurable systems that are easy to maintain.",
            ],
            'availability' => [
                'es' => 'Disponible para nuevos proyectos',
                'en' => 'Available for new projects',
            ],
            'degree' => [
                'es' => 'Tecnólogo en Desarrollo de Software',
                'en' => 'Software Development Technologist',
            ],
            'location' => 'España',
            'email' => 'contacto@antoniobc.net',
            'whatsapp' => '+593961372191',
            'avatar_path' => 'profile/profile-img.webp',
            'cv_path' => 'cv/cvAntonioBenalcazar.pdf',
        ]);
    }

    protected function seedSocials(): void
    {
        $socials = [
            ['platform' => 'github', 'label' => 'GitHub', 'url' => 'https://github.com/toninow', 'sort' => 1],
            ['platform' => 'linkedin', 'label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/in/antonio-jose-benalc%C3%A1zar-cabrejo-9870444b/', 'sort' => 2],
            ['platform' => 'twitter', 'label' => 'Twitter', 'url' => 'https://twitter.com/SoloEscuchoRap', 'sort' => 3],
            ['platform' => 'instagram', 'label' => 'Instagram', 'url' => 'https://www.instagram.com/antdev0010/', 'sort' => 4],
            ['platform' => 'facebook', 'label' => 'Facebook', 'url' => 'https://www.facebook.com/devdev00100101/', 'sort' => 5],
        ];

        foreach ($socials as $social) {
            SocialLink::updateOrCreate(['url' => $social['url']], $social);
        }
    }

    /** @return array<string, Technology> */
    protected function seedTechnologies(): array
    {
        $map = [
            'backend' => ['PHP', 'Laravel', 'Livewire'],
            'frontend' => ['Tailwind CSS', 'Blade', 'JavaScript', 'HTML5', 'CSS3', 'Bootstrap', 'Flutter'],
            'data' => ['MySQL', 'PostgreSQL', 'APIs REST'],
            'erp' => ['Dolibarr', 'PrestaShop', 'WordPress', 'Bitrix', 'Moodle'],
            'infra' => ['Linux', 'Docker', 'Git', 'Gitea', 'Apache', 'Restic'],
            'ia' => ['IA (GPT · Claude)', 'Cursor', 'Automatización con IA'],
            'tools' => ['Microsoft 365'],
        ];

        $technologies = [];
        $sort = 0;
        foreach ($map as $area => $names) {
            foreach ($names as $name) {
                $slug = Str::slug($name);
                $technologies[$slug] = Technology::updateOrCreate(
                    ['slug' => $slug],
                    ['name' => $name, 'area' => $area, 'sort' => $sort++]
                );
            }
        }

        return $technologies;
    }

    protected function seedSkills(): void
    {
        $groups = [
            ['name' => ['es' => 'Backend', 'en' => 'Backend'], 'skills' => ['PHP', 'Laravel', 'Livewire']],
            ['name' => ['es' => 'Frontend & móvil', 'en' => 'Frontend & mobile'], 'skills' => ['HTML5', 'CSS3', 'JavaScript', 'Tailwind CSS', 'Flutter']],
            ['name' => ['es' => 'Datos & APIs', 'en' => 'Data & APIs'], 'skills' => ['MySQL', 'PostgreSQL', 'APIs REST']],
            ['name' => ['es' => 'ERP & ecommerce', 'en' => 'ERP & ecommerce'], 'skills' => ['Dolibarr', 'PrestaShop', 'WordPress']],
            ['name' => ['es' => 'Infraestructura', 'en' => 'Infrastructure'], 'skills' => ['Linux', 'Docker', 'Git', 'Restic']],
            ['name' => ['es' => 'IA & productividad', 'en' => 'AI & productivity'], 'skills' => ['Uso de IA (GPT · Claude)', 'Cursor', 'Automatización con IA', 'Prompting efectivo']],
        ];

        // Idempotent refresh: seed content is not hand-edited in the admin, so a
        // clean rebuild keeps groups/skills in sync without duplicating rows.
        Skill::query()->delete();
        SkillGroup::query()->delete();

        $sort = 0;
        foreach ($groups as $g) {
            $group = SkillGroup::create(['name' => $g['name'], 'sort' => $sort++]);
            foreach ($g['skills'] as $i => $skillName) {
                Skill::create(['skill_group_id' => $group->id, 'name' => $skillName, 'sort' => $i]);
            }
        }
    }

    /** @param array<string, Technology> $tech */
    protected function seedServices(array $tech): void
    {
        $services = [
            [
                'title' => ['es' => 'Desarrollo de sistemas internos', 'en' => 'Internal systems development'],
                'summary' => ['es' => 'Aplicaciones a medida para el trabajo diario de tu equipo.', 'en' => 'Custom applications for your team\'s daily work.'],
                'problems' => ['es' => ['Procesos manuales repetitivos', 'Información dispersa en hojas de cálculo', 'Falta de trazabilidad'], 'en' => ['Repetitive manual processes', 'Data scattered across spreadsheets', 'Lack of traceability']],
                'includes' => ['es' => ['Análisis del proceso', 'Diseño de la solución', 'Desarrollo e integración'], 'en' => ['Process analysis', 'Solution design', 'Development and integration']],
                'tech' => ['laravel', 'livewire', 'mysql'],
            ],
            [
                'title' => ['es' => 'Desarrollo web y plataformas', 'en' => 'Web development & platforms'],
                'summary' => ['es' => 'Sitios web, plataformas y landings rápidas, medibles y bien posicionadas.', 'en' => 'Fast, measurable and well-ranked websites, platforms and landing pages.'],
                'problems' => ['es' => ['Webs lentas o desactualizadas', 'Poca visibilidad en buscadores', 'Landings que no convierten'], 'en' => ['Slow or outdated sites', 'Low search visibility', 'Landings that do not convert']],
                'tech' => ['laravel', 'wordpress', 'javascript'],
            ],
            [
                'title' => ['es' => 'Automatización de procesos con IA', 'en' => 'Process automation with AI'],
                'summary' => ['es' => 'Elimino tareas manuales conectando tus sistemas y apoyándome en IA.', 'en' => 'I remove manual tasks by connecting your systems and leveraging AI.'],
                'problems' => ['es' => ['Trabajo manual repetido cada día', 'Errores por copiar y pegar', 'Tareas que nadie quiere hacer'], 'en' => ['Manual work repeated every day', 'Copy-paste errors', 'Tasks nobody wants to do']],
                'tech' => ['php', 'apis-rest'],
            ],
            [
                'title' => ['es' => 'Integración de ERP y ecommerce', 'en' => 'ERP and ecommerce integration'],
                'summary' => ['es' => 'Sincronización entre Dolibarr y PrestaShop: productos, precios y stock.', 'en' => 'Sync between Dolibarr and PrestaShop: products, prices and stock.'],
                'problems' => ['es' => ['Stock descuadrado entre web y ERP', 'Precios que no coinciden', 'Doble trabajo de introducción de datos'], 'en' => ['Stock mismatch between web and ERP', 'Prices out of sync', 'Duplicate data entry']],
                'tech' => ['dolibarr', 'prestashop', 'apis-rest'],
            ],
            [
                'title' => ['es' => 'Herramientas de stock e inventario', 'en' => 'Stock and inventory tools'],
                'summary' => ['es' => 'Consulta de existencias por almacén, EAN y referencia.', 'en' => 'Stock lookups by warehouse, EAN and reference.'],
                'tech' => ['dolibarr', 'flutter', 'apis-rest'],
            ],
            [
                'title' => ['es' => 'Automatización de catálogos', 'en' => 'Catalog automation'],
                'summary' => ['es' => 'Proceso, valido y enriquezco catálogos de proveedores.', 'en' => 'I process, validate and enrich supplier catalogs.'],
                'tech' => ['php', 'mysql'],
            ],
            [
                'title' => ['es' => 'Desarrollo de módulos', 'en' => 'Module development'],
                'summary' => ['es' => 'Módulos y extensiones para Dolibarr, PrestaShop y aplicaciones existentes.', 'en' => 'Modules and extensions for Dolibarr, PrestaShop and existing apps.'],
                'tech' => ['php', 'dolibarr', 'prestashop'],
            ],
            [
                'title' => ['es' => 'Auditoría y diagnóstico técnico', 'en' => 'Technical audit and diagnosis'],
                'summary' => ['es' => 'Reviso tus sistemas y detecto puntos de mejora y riesgo.', 'en' => 'I review your systems and find improvement and risk points.'],
                'tech' => ['linux', 'mysql'],
            ],
            [
                'title' => ['es' => 'Infraestructura, Git y backups', 'en' => 'Infrastructure, Git and backups'],
                'summary' => ['es' => 'Servidores Linux, Docker, repositorios privados y copias verificables.', 'en' => 'Linux servers, Docker, private repositories and verifiable backups.'],
                'tech' => ['linux', 'docker', 'gitea', 'restic'],
            ],
        ];

        foreach ($services as $i => $s) {
            $service = Service::updateOrCreate(
                ['slug' => Str::slug($s['title']['es'])],
                [
                    'title' => $s['title'],
                    'summary' => $s['summary'] ?? null,
                    'problems' => $s['problems'] ?? null,
                    'includes' => $s['includes'] ?? null,
                    'sort' => $i,
                    'is_published' => true,
                ]
            );
            $ids = collect($s['tech'] ?? [])->map(fn ($slug) => $tech[$slug]->id ?? null)->filter()->all();
            $service->technologies()->sync($ids);
        }
    }

    /** @return array<string, ProjectCategory> */
    protected function seedCategories(): array
    {
        $categories = [
            'aplicaciones-internas' => ['es' => 'Aplicaciones internas', 'en' => 'Internal applications'],
            'erp' => ['es' => 'ERP', 'en' => 'ERP'],
            'ecommerce' => ['es' => 'Ecommerce', 'en' => 'Ecommerce'],
            'automatizacion' => ['es' => 'Automatización', 'en' => 'Automation'],
            'inventario' => ['es' => 'Inventario', 'en' => 'Inventory'],
            'integraciones' => ['es' => 'Integraciones', 'en' => 'Integrations'],
            'infraestructura' => ['es' => 'Infraestructura', 'en' => 'Infrastructure'],
            'web' => ['es' => 'Web', 'en' => 'Web'],
            'proyectos-anteriores' => ['es' => 'Proyectos anteriores', 'en' => 'Previous projects'],
        ];

        $result = [];
        $sort = 0;
        foreach ($categories as $slug => $name) {
            $result[$slug] = ProjectCategory::updateOrCreate(['slug' => $slug], ['name' => $name, 'sort' => $sort++]);
        }

        return $result;
    }

    /**
     * @param  array<string, ProjectCategory>  $cat
     * @param  array<string, Technology>  $tech
     */
    protected function seedFeaturedProjects(array $cat, array $tech): void
    {
        $projects = [
            [
                'slug' => 'mp-proveedores',
                'category' => 'aplicaciones-internas',
                'size' => 'large',
                'name' => ['es' => 'MP Proveedores', 'en' => 'MP Suppliers'],
                'summary' => ['es' => 'Sistema interno para relacionar productos del ERP con catálogos de proveedores.', 'en' => 'Internal system to match ERP products with supplier catalogs.'],
                'problem' => ['es' => 'Relacionar grandes catálogos de proveedores con los productos del ERP era un trabajo manual, lento y propenso a errores.', 'en' => 'Matching large supplier catalogs with ERP products was manual, slow and error-prone.'],
                'solution' => ['es' => 'Una herramienta que procesa grandes catálogos, detecta conflictos, relaciona referencias y códigos de barras, y ofrece una interfaz para revisar coincidencias y actualizar precios.', 'en' => 'A tool that processes large catalogs, detects conflicts, matches references and barcodes, and provides an interface to review matches and update prices.'],
                'tech' => ['php', 'laravel', 'mysql'],
                'type' => 'Aplicación interna',
            ],
            [
                'slug' => 'control-stock-dolibarr',
                'category' => 'inventario',
                'size' => 'medium',
                'name' => ['es' => 'Control de stock Dolibarr', 'en' => 'Dolibarr stock control'],
                'summary' => ['es' => 'Aplicación móvil interna para consultar inventario en tiempo real.', 'en' => 'Internal mobile app to check inventory in real time.'],
                'problem' => ['es' => 'Consultar el stock por almacén requería acceder al ERP de escritorio, poco práctico en almacén.', 'en' => 'Checking stock by warehouse required the desktop ERP, impractical on the warehouse floor.'],
                'solution' => ['es' => 'App mobile-first con búsqueda por EAN, UPC, referencia y nombre, stock por almacén, integración con la API y control de permisos.', 'en' => 'Mobile-first app with search by EAN, UPC, reference and name, stock by warehouse, API integration and permission control.'],
                'tech' => ['flutter', 'dolibarr', 'apis-rest'],
                'type' => 'Inventario',
            ],
            [
                'slug' => 'integracion-prestashop-dolibarr',
                'category' => 'integraciones',
                'size' => 'medium',
                'name' => ['es' => 'Integración PrestaShop y Dolibarr', 'en' => 'PrestaShop and Dolibarr integration'],
                'summary' => ['es' => 'Sincronización entre ecommerce y ERP: productos, precios, stock y datos web.', 'en' => 'Sync between ecommerce and ERP: products, prices, stock and web data.'],
                'problem' => ['es' => 'El ecommerce y el ERP mantenían datos por separado, generando descuadres y doble trabajo.', 'en' => 'Ecommerce and ERP kept data separately, causing mismatches and duplicate work.'],
                'solution' => ['es' => 'Sincronización de productos, precios y stock con diagnóstico de errores y compatibilidad entre sistemas.', 'en' => 'Product, price and stock synchronization with error diagnostics and cross-system compatibility.'],
                'tech' => ['dolibarr', 'prestashop', 'php', 'apis-rest'],
                'type' => 'Integración',
            ],
            [
                'slug' => 'gitea-autogestionado',
                'category' => 'infraestructura',
                'size' => 'compact',
                'name' => ['es' => 'Gitea autogestionado', 'en' => 'Self-hosted Gitea'],
                'summary' => ['es' => 'Infraestructura privada para repositorios de código.', 'en' => 'Private infrastructure for code repositories.'],
                'solution' => ['es' => 'Gitea sobre Docker con PostgreSQL, Apache, HTTPS, acceso SSH, backups y publicación organizada de repositorios.', 'en' => 'Gitea on Docker with PostgreSQL, Apache, HTTPS, SSH access, backups and organized repository publishing.'],
                'tech' => ['docker', 'gitea', 'postgresql', 'apache', 'linux'],
                'type' => 'Infraestructura',
            ],
            [
                'slug' => 'backups-restic',
                'category' => 'infraestructura',
                'size' => 'compact',
                'name' => ['es' => 'Sistema de backups con Restic', 'en' => 'Backup system with Restic'],
                'summary' => ['es' => 'Diseño de copias de seguridad verificables y recuperables.', 'en' => 'Verifiable, recoverable backup design.'],
                'solution' => ['es' => 'Copias de web, bases de datos y repositorios con políticas de retención, verificación y pruebas de recuperación.', 'en' => 'Backups of web, databases and repositories with retention policies, verification and recovery testing.'],
                'tech' => ['restic', 'linux'],
                'type' => 'Infraestructura',
            ],
            [
                'slug' => 'automatizacion-catalogos-proveedores',
                'category' => 'automatizacion',
                'size' => 'compact',
                'name' => ['es' => 'Automatización de catálogos de proveedores', 'en' => 'Supplier catalog automation'],
                'summary' => ['es' => 'Procesamiento y enriquecimiento de archivos Excel y catálogos web.', 'en' => 'Processing and enrichment of Excel files and web catalogs.'],
                'solution' => ['es' => 'Procesamiento de SKU, EAN, precios, disponibilidad e imágenes, con validación y exportación.', 'en' => 'Processing of SKU, EAN, prices, availability and images, with validation and export.'],
                'tech' => ['php', 'mysql'],
                'type' => 'Automatización',
            ],
        ];

        foreach ($projects as $i => $p) {
            $project = Project::updateOrCreate(['slug' => $p['slug']], [
                'project_category_id' => $cat[$p['category']]->id,
                'name' => $p['name'],
                'summary' => $p['summary'] ?? null,
                'problem' => $p['problem'] ?? null,
                'solution' => $p['solution'] ?? null,
                'project_type' => $p['type'] ?? null,
                'status' => 'published',
                'visibility' => 'public',
                'is_featured' => true,
                'featured_size' => $p['size'],
                'sort' => $i,
                'year' => 2025,
                'published_at' => now(),
            ]);
            $ids = collect($p['tech'])->map(fn ($slug) => $tech[$slug]->id ?? null)->filter()->all();
            $project->technologies()->sync($ids);
        }
    }

    /**
     * @param  array<string, ProjectCategory>  $cat
     * @param  array<string, Technology>  $tech
     */
    protected function seedLegacyProjects(array $cat, array $tech): void
    {
        $legacy = [
            ['Sitio web Istcre', 'Website for the Ecuadorian Red Cross Technological Institute', 'Sitio Web del Instituto Superior Tecnológico Cruz Roja Ecuatoriana', 2020, 'web', ['wordpress'], 'sitio_web-Istcre.jpg', 'https://www.cruzrojainstituto.edu.ec/'],
            ['Juego educativo FONAP', 'Interactive learning game developed for the FONAP Foundation', 'Videojuego interactivo para aprendizaje desarrollado para la Fundación FONAP', 2021, 'web', [], 'port2.webp', 'https://fonap-game.web.app/'],
            ['Revista ISTCRE', "Digital platform for the institute's academic journal", 'Plataforma digital para la revista académica del instituto', 2021, 'web', ['laravel', 'mysql'], 'port3.webp', 'https://www.revistaacademica-istcre.edu.ec/'],
            ['Admisiones ISTCRE', "Landing page for the institute's admission process", 'Landing page para el proceso de admisiones del instituto', 2022, 'web', ['php', 'html5', 'css3', 'javascript', 'mysql'], 'port4.webp', 'https://admisiones.cruzrojainstituto.edu.ec/'],
            ['Educación Continua ISTCRE', "Web system for the institute's continuing education courses", 'Sistema web para los cursos de educación continua del instituto', 2022, 'web', ['php', 'html5', 'css3', 'javascript', 'mysql'], 'port5.webp', 'https://www.cruzrojainstituto.edu.ec/cec'],
            ['Instituto de la ciudad de Quito', 'Research website for the Quito City Institute', 'Sitio Web de investigación del Instituto de la ciudad de Quito', 2022, 'web', ['wordpress'], 'instituto-ciudad.jpg', 'https://institutodelaciudad.com.ec/'],
            ['Constructora Carei Home', 'Carei Home real estate development and management website', 'Sitio web Carei Home, desarrollo y gestión de proyectos inmobiliarios', 2023, 'web', ['wordpress'], 'carei.jpg', null],
            ['Productos Calma', 'Informational website for anti-stress blanket sales', 'Sitio web informativo de venta de mantas antiestrés', 2023, 'web', ['wordpress'], 'productos_calma.jpg', 'https://productoscalma.net/'],
            ['Landing Netlife Internet', 'Website for the Netlife internet contract request form', 'Formulario de solicitud de contrato de internet Netlife', 2023, 'web', ['wordpress'], 'netlifenetec.jpg', 'https://internetnetlife.net.ec/'],
            ['Landing Netlife Internet (com.ec)', 'Website for the Netlife internet contract request form', 'Formulario de solicitud de contrato de internet Netlife', 2024, 'web', ['php', 'html5', 'css3', 'javascript', 'bitrix'], 'netlifeinternetcomec.jpg', 'https://netlifeinternet.com.ec/'],
            ['Landing Netlife Internet (ec)', 'Website for the Netlife internet contract request form', 'Formulario de solicitud de contrato de internet Netlife', 2024, 'web', ['php', 'html5', 'css3', 'javascript', 'bitrix'], 'netlifeinternetec.jpg', 'https://netlifeinternet.ec/'],
            ['Landing Fibramax Internet', 'Website for the Fibramax internet contract request form', 'Formulario de solicitud de contrato de internet Fibramax', 2025, 'web', ['php', 'html5', 'css3', 'javascript', 'bitrix'], 'fibramaxnet.jpg', 'https://ventas-fibramax.net/'],
            ['Landing Celerity Internet', 'Website for the Celerity internet contract request form', 'Formulario de solicitud de contrato de internet Celerity', 2025, 'web', ['wordpress'], 'ventascelerity.jpg', 'https://ventas-celerity.com/'],
            ['Landing Promociones Fibramax', 'Website for the Fibramax internet contract request form', 'Formulario de solicitud de contrato de internet Fibramax', 2025, 'web', ['wordpress'], 'promociones-fibramax.jpg', 'https://promociones-fibramax.com/'],
        ];

        foreach ($legacy as $i => [$nameEs, $summaryEn, $summaryEs, $year, $catSlug, $techSlugs, $image, $url]) {
            $project = Project::updateOrCreate(['slug' => Str::slug($nameEs).'-'.$year], [
                'project_category_id' => $cat['proyectos-anteriores']->id,
                'name' => ['es' => $nameEs, 'en' => $nameEs],
                'summary' => ['es' => $summaryEs, 'en' => $summaryEn],
                'year' => $year,
                'url' => $url,
                'project_type' => 'Web',
                'main_image_path' => 'projects/'.$image,
                'status' => 'published',
                'visibility' => 'public',
                'is_featured' => false,
                'sort' => 100 + $i,
                'published_at' => now(),
            ]);
            $ids = collect($techSlugs)->map(fn ($slug) => $tech[$slug]->id ?? null)->filter()->all();
            $project->technologies()->sync($ids);
        }
    }

    protected function seedExperience(): void
    {
        Experience::updateOrCreate(
            ['company' => 'Trabajo actual'],
            [
                'role' => ['es' => 'Desarrollo, automatización e integraciones', 'en' => 'Development, automation and integrations'],
                'description' => ['es' => 'Sistemas internos, integración ERP-ecommerce, inventario e infraestructura.', 'en' => 'Internal systems, ERP-ecommerce integration, inventory and infrastructure.'],
                'start_date' => '2022',
                'is_current' => true,
                'sort' => 0,
            ]
        );

        Experience::updateOrCreate(
            ['company' => 'Proyectos web'],
            [
                'role' => ['es' => 'Desarrollo web y sistemas', 'en' => 'Web and systems development'],
                'description' => ['es' => 'Sitios web, sistemas y landings para instituciones y empresas en Ecuador.', 'en' => 'Websites, systems and landing pages for institutions and companies in Ecuador.'],
                'start_date' => '2019',
                'end_date' => '2022',
                'sort' => 1,
            ]
        );
    }

    protected function seedEducation(): void
    {
        Education::updateOrCreate(
            ['institution' => 'Instituto Superior Tecnológico Cruz Roja Ecuatoriana'],
            [
                'title' => ['es' => 'Tecnólogo en Desarrollo de Software', 'en' => 'Software Development Technologist'],
                'sort' => 0,
            ]
        );
    }
}
