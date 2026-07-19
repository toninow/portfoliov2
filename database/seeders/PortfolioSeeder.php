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
                'es' => 'Desarrollador de software · Backend, automatización e integraciones',
                'en' => 'Software Developer · Backend, Automation and Integrations',
            ],
            'bio' => [
                'es' => 'Desarrollo aplicaciones internas, automatizaciones e integraciones entre ERP, ecommerce, inventario, proveedores y APIs. Trabajo principalmente con PHP, Laravel, bases de datos y servidores Linux, desde el análisis del problema hasta el despliegue y la mejora continua.',
                'en' => 'I build internal applications, automations and integrations across ERP, ecommerce, inventory, suppliers and APIs. I work mainly with PHP, Laravel, databases and Linux servers, from problem analysis through deployment and continuous improvement.',
            ],
            'about_long' => [
                'es' => "Soy desarrollador de software especializado en aplicaciones internas, automatización e integración de sistemas empresariales.\n\nHe trabajado en desarrollo web, plataformas educativas, soporte tecnológico e infraestructura. Actualmente centro mi trabajo en conectar ERP y ecommerce, organizar catálogos de proveedores, mejorar el control de inventario y transformar tareas manuales en procesos más fiables.\n\nUtilizo herramientas de inteligencia artificial como apoyo para analizar, desarrollar, revisar y documentar soluciones, manteniendo la validación técnica y la responsabilidad sobre el resultado.",
                'en' => "I am a software developer specialised in internal applications, automation and business system integration.\n\nI have worked on web development, educational platforms, technical support and infrastructure. Today I focus on connecting ERP and ecommerce, organising supplier catalogs, improving inventory control and turning manual tasks into more reliable processes.\n\nI use AI tools to support analysis, development, review and documentation, while keeping technical validation and ownership of the result.",
            ],
            'availability' => [
                'es' => 'Disponible para oportunidades profesionales y proyectos seleccionados.',
                'en' => 'Open to professional opportunities and selected projects.',
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
            'backend' => ['PHP', 'Python', 'Java', 'Laravel', 'Spring Boot', 'Django', 'Livewire', 'Power Fx'],
            'frontend' => ['JavaScript', 'HTML5', 'CSS3', 'Tailwind CSS', 'Blade', 'Bootstrap', 'Flutter', 'Dart', 'React Native'],
            'data' => ['MySQL', 'PostgreSQL', 'SQL Server', 'APIs REST'],
            'erp' => ['Dolibarr', 'PrestaShop', 'WordPress', 'Moodle', 'Bitrix'],
            'infra' => ['Linux', 'Docker', 'Git', 'Gitea', 'Apache', 'Restic'],
            'ia' => ['IA (GPT · Claude)', 'Cursor', 'Automatización con IA'],
            'tools' => ['Microsoft 365', 'Google Workspace', 'Scrum'],
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
                'lifecycle' => 'production',
                'year' => 2025,
                'name' => ['es' => 'MP Proveedores', 'en' => 'MP Suppliers'],
                'summary' => [
                    'es' => 'Plataforma interna para relacionar catálogos de proveedores con los productos del ERP y revisar conflictos de referencias, códigos de barras y precios.',
                    'en' => 'Internal platform to match supplier catalogs with ERP products and review conflicts in references, barcodes and prices.',
                ],
                'outcome_headline' => [
                    'es' => 'Relacionar grandes catálogos con el ERP dejando los conflictos visibles y controlados.',
                    'en' => 'Matching large catalogs to the ERP while keeping conflicts visible and controlled.',
                ],
                'context' => [
                    'es' => 'En un entorno de compra con múltiples proveedores, los catálogos llegan en formatos distintos y deben contrastarse con el ERP (Dolibarr) antes de actualizar precios o fichas.',
                    'en' => 'In a purchasing environment with multiple suppliers, catalogs arrive in different formats and must be checked against the ERP (Dolibarr) before prices or product data are updated.',
                ],
                'problem' => [
                    'es' => "Relacionar catálogos de proveedores con los productos del ERP era un trabajo manual, lento y propenso a errores.\nUn mismo código de barras podía asociarse a referencias distintas.\nLos conflictos se perdían entre hojas de cálculo y no había un flujo claro entre coincidencias seguras y casos dudosos.",
                    'en' => "Matching supplier catalogs with ERP products was manual, slow and error-prone.\nThe same barcode could map to different references.\nConflicts were lost in spreadsheets and there was no clear flow between safe matches and doubtful cases.",
                ],
                'responsibilities' => [
                    'es' => 'Análisis funcional, arquitectura, modelado de datos, backend, interfaz de revisión y criterios de coincidencia/conflicto.',
                    'en' => 'Functional analysis, architecture, data modeling, backend, review UI and match/conflict criteria.',
                ],
                'role' => [
                    'es' => 'Análisis, arquitectura, backend, integración y UX',
                    'en' => 'Analysis, architecture, backend, integration and UX',
                ],
                'solution' => [
                    'es' => 'Una herramienta que importa y normaliza catálogos, valida referencias y EAN, genera coincidencias, separa casos seguros de dudosos y permite revisión humana antes de actualizar precios de forma controlada.',
                    'en' => 'A tool that imports and normalizes catalogs, validates references and EANs, generates matches, separates safe from doubtful cases and allows human review before controlled price updates.',
                ],
                'workflow_steps' => [
                    ['label' => 'Importación del catálogo', 'description' => 'Carga del archivo o fuente del proveedor.'],
                    ['label' => 'Normalización', 'description' => 'Homogeneización de campos, referencias y formatos.'],
                    ['label' => 'Validación de referencias y EAN', 'description' => 'Detección de datos incompletos o inconsistentes.'],
                    ['label' => 'Generación de coincidencias', 'description' => 'Emparejado con productos del ERP.'],
                    ['label' => 'Separación seguros / dudosos', 'description' => 'Los conflictos quedan visibles para revisión.'],
                    ['label' => 'Revisión humana', 'description' => 'Interfaz para resolver casos ambiguos.'],
                    ['label' => 'Actualización controlada', 'description' => 'Aplicación de cambios de precio o ficha con trazabilidad.'],
                ],
                'challenges' => [
                    [
                        'difficulty' => 'Un mismo código de barras podía aparecer asociado a referencias diferentes.',
                        'decision' => 'Separar coincidencias automáticas de casos que requieren revisión humana.',
                        'outcome' => 'Los casos seguros pueden procesarse sin ocultar los conflictos.',
                    ],
                ],
                'qualitative_results' => [
                    ['label' => 'Menos trabajo manual de emparejado'],
                    ['label' => 'Conflictos visibles y trazables'],
                    ['label' => 'Proceso repetible entre proveedores'],
                ],
                'tech' => ['php', 'laravel', 'mysql', 'dolibarr'],
                'type' => 'Aplicación interna',
            ],
            [
                'slug' => 'control-stock-dolibarr',
                'category' => 'inventario',
                'size' => 'medium',
                'lifecycle' => 'production',
                'year' => 2025,
                'name' => ['es' => 'Control de stock Dolibarr', 'en' => 'Dolibarr stock control'],
                'summary' => [
                    'es' => 'Aplicación móvil interna para consultar inventario en tiempo real desde el almacén.',
                    'en' => 'Internal mobile app to check inventory in real time from the warehouse floor.',
                ],
                'outcome_headline' => [
                    'es' => 'Consultar stock por almacén desde el móvil, sin abrir el ERP de escritorio.',
                    'en' => 'Check warehouse stock from a phone, without opening the desktop ERP.',
                ],
                'context' => [
                    'es' => 'El equipo de almacén necesitaba consultar existencias durante el trabajo diario, lejos del puesto con el ERP de escritorio.',
                    'en' => 'Warehouse staff needed stock lookups during daily work, away from the desktop ERP workstation.',
                ],
                'problem' => [
                    'es' => 'Consultar el stock por almacén requería acceder al ERP de escritorio, poco práctico en planta. Buscar por EAN, UPC, referencia o nombre no era ágil fuera del puesto fijo.',
                    'en' => 'Checking stock by warehouse required the desktop ERP, impractical on the floor. Searching by EAN, UPC, reference or name was not agile away from a fixed desk.',
                ],
                'responsibilities' => [
                    'es' => 'Diseño mobile-first, integración con la API de Dolibarr, autenticación, roles y modo de consulta.',
                    'en' => 'Mobile-first design, Dolibarr API integration, authentication, roles and read-focused lookup mode.',
                ],
                'role' => [
                    'es' => 'Producto, backend de integración y app móvil',
                    'en' => 'Product, integration backend and mobile app',
                ],
                'solution' => [
                    'es' => 'App mobile-first (Flutter) con búsqueda por EAN, UPC, referencia y nombre, stock por almacén, integración con la API de Dolibarr y control de permisos orientado a consulta.',
                    'en' => 'Mobile-first Flutter app with search by EAN, UPC, reference and name, stock by warehouse, Dolibarr API integration and permission control focused on lookup.',
                ],
                'qualitative_results' => [
                    ['label' => 'Acceso a stock desde móvil'],
                    ['label' => 'Búsqueda por código y nombre'],
                    ['label' => 'Consulta por almacén'],
                ],
                'tech' => ['flutter', 'dolibarr', 'apis-rest'],
                'type' => 'Inventario',
            ],
            [
                'slug' => 'integracion-prestashop-dolibarr',
                'category' => 'integraciones',
                'size' => 'medium',
                'lifecycle' => 'production',
                'year' => 2025,
                'name' => ['es' => 'Integración PrestaShop y Dolibarr', 'en' => 'PrestaShop and Dolibarr integration'],
                'summary' => [
                    'es' => 'Sincronización entre ecommerce y ERP: productos, precios, stock y datos web.',
                    'en' => 'Sync between ecommerce and ERP: products, prices, stock and web data.',
                ],
                'outcome_headline' => [
                    'es' => 'Una sola fuente de verdad entre PrestaShop y Dolibarr, con diagnóstico cuando algo falla.',
                    'en' => 'A single source of truth between PrestaShop and Dolibarr, with diagnostics when something fails.',
                ],
                'context' => [
                    'es' => 'La tienda online y el ERP mantenían catálogos y stock en paralelo. Cualquier desfase generaba errores de venta o trabajo duplicado.',
                    'en' => 'The online store and ERP kept catalogs and stock in parallel. Any drift caused sales errors or duplicate work.',
                ],
                'problem' => [
                    'es' => 'El ecommerce y el ERP mantenían datos por separado, generando descuadres de stock/precio y doble trabajo de introducción. Faltaba diagnóstico claro cuando una sincronización fallaba.',
                    'en' => 'Ecommerce and ERP kept data separately, causing stock/price mismatches and duplicate data entry. There was no clear diagnosis when a sync failed.',
                ],
                'responsibilities' => [
                    'es' => 'Diseño de la sincronización, reglas de dirección de datos, manejo de errores y compatibilidad entre versiones de ambos sistemas.',
                    'en' => 'Sync design, data-direction rules, error handling and compatibility across versions of both systems.',
                ],
                'role' => [
                    'es' => 'Integración, backend y diagnóstico',
                    'en' => 'Integration, backend and diagnostics',
                ],
                'solution' => [
                    'es' => 'Sincronización de productos, precios y stock con diagnóstico de errores, controles para no romper producción y compatibilidad entre sistemas.',
                    'en' => 'Product, price and stock synchronization with error diagnostics, safeguards for production and cross-system compatibility.',
                ],
                'qualitative_results' => [
                    ['label' => 'Menos doble introducción de datos'],
                    ['label' => 'Descuadres más fáciles de diagnosticar'],
                    ['label' => 'Sincronización repetible'],
                ],
                'tech' => ['dolibarr', 'prestashop', 'php', 'apis-rest'],
                'type' => 'Integración',
            ],
            [
                'slug' => 'gitea-autogestionado',
                'category' => 'infraestructura',
                'size' => 'compact',
                'lifecycle' => 'production',
                'year' => 2026,
                'period' => '2026',
                'name' => ['es' => 'Gitea autogestionado', 'en' => 'Self-hosted Gitea'],
                'summary' => [
                    'es' => 'Infraestructura privada para repositorios de código con acceso controlado.',
                    'en' => 'Private infrastructure for code repositories with controlled access.',
                ],
                'outcome_headline' => [
                    'es' => 'Repositorios privados bajo control propio, con HTTPS, SSH y backups.',
                    'en' => 'Private repositories under your own control, with HTTPS, SSH and backups.',
                ],
                'context' => [
                    'es' => 'Se necesitaba un remoto Git privado, organizado y recuperable, sin depender exclusivamente de un SaaS externo.',
                    'en' => 'A private, organized and recoverable Git remote was needed without relying solely on an external SaaS.',
                ],
                'problem' => [
                    'es' => 'Faltaba una infraestructura propia para repositorios privados, con acceso SSH/HTTPS, organización clara y copias de seguridad.',
                    'en' => 'There was no self-hosted setup for private repositories with SSH/HTTPS access, clear organization and backups.',
                ],
                'responsibilities' => [
                    'es' => 'Diseño e implementación de la infraestructura: Docker, PostgreSQL, Apache como proxy, HTTPS, SSH, organización de repos y backups.',
                    'en' => 'Infrastructure design and implementation: Docker, PostgreSQL, Apache as proxy, HTTPS, SSH, repo organization and backups.',
                ],
                'role' => [
                    'es' => 'Infraestructura y despliegue',
                    'en' => 'Infrastructure and deployment',
                ],
                'solution' => [
                    'es' => 'Gitea sobre Docker con PostgreSQL, Apache como proxy, HTTPS, acceso SSH, organización de repositorios y backups.',
                    'en' => 'Gitea on Docker with PostgreSQL, Apache as proxy, HTTPS, SSH access, repository organization and backups.',
                ],
                'qualitative_results' => [
                    ['label' => 'Control privado del código'],
                    ['label' => 'Acceso HTTPS y SSH'],
                    ['label' => 'Base preparada para backups'],
                ],
                'tech' => ['docker', 'gitea', 'postgresql', 'apache', 'linux'],
                'type' => 'Infraestructura',
            ],
            [
                'slug' => 'backups-restic',
                'category' => 'infraestructura',
                'size' => 'compact',
                'lifecycle' => 'implementation',
                'is_ongoing' => true,
                'year' => 2025,
                'name' => ['es' => 'Sistema de backups con Restic', 'en' => 'Backup system with Restic'],
                'summary' => [
                    'es' => 'Diseño e implementación de copias de seguridad verificables y recuperables.',
                    'en' => 'Design and implementation of verifiable, recoverable backups.',
                ],
                'outcome_headline' => [
                    'es' => 'Un diseño de backups con retención, verificación y recuperación — en implementación.',
                    'en' => 'A backup design with retention, verification and recovery — currently being implemented.',
                ],
                'context' => [
                    'es' => 'Web, bases de datos y repositorios necesitan copias verificables, no solo archivos copiados a otro disco.',
                    'en' => 'Web, databases and repositories need verifiable backups, not just files copied to another disk.',
                ],
                'problem' => [
                    'es' => 'Sin políticas claras de retención, verificación y pruebas de restauración, una copia puede existir y aun así no servir cuando hace falta.',
                    'en' => 'Without clear retention, verification and restore tests, a copy may exist and still fail when it is needed.',
                ],
                'responsibilities' => [
                    'es' => 'Auditoría del alcance, diseño de la política, elección de Restic y definición de verificación/recuperación. Estado: en implementación.',
                    'en' => 'Scope audit, policy design, Restic choice and definition of verification/recovery. Status: in implementation.',
                ],
                'role' => [
                    'es' => 'Diseño de backups e infraestructura',
                    'en' => 'Backup design and infrastructure',
                ],
                'solution' => [
                    'es' => 'Diseño de copias de web, bases de datos y repositorios con Restic, políticas de retención, verificación y pruebas de recuperación. Aún en fase de implementación.',
                    'en' => 'Design of web, database and repository backups with Restic, retention policies, verification and recovery testing. Still in implementation.',
                ],
                'qualitative_results' => [
                    ['label' => 'Alcance de backup definido'],
                    ['label' => 'Política de retención diseñada'],
                    ['label' => 'Verificación y restauración previstas'],
                ],
                'tech' => ['restic', 'linux'],
                'type' => 'Infraestructura',
            ],
            [
                'slug' => 'automatizacion-catalogos-proveedores',
                'category' => 'automatizacion',
                'size' => 'compact',
                'lifecycle' => 'production',
                'year' => 2025,
                'name' => ['es' => 'Automatización de catálogos de proveedores', 'en' => 'Supplier catalog automation'],
                'summary' => [
                    'es' => 'Procesamiento y enriquecimiento de archivos Excel, CSV y catálogos web de proveedores.',
                    'en' => 'Processing and enrichment of Excel, CSV and web supplier catalogs.',
                ],
                'outcome_headline' => [
                    'es' => 'Pasar de catálogos manuales a un flujo con validación, errores visibles y exportación lista.',
                    'en' => 'Moving from manual catalogs to a flow with validation, visible errors and ready-to-use export.',
                ],
                'context' => [
                    'es' => 'Los proveedores envían catálogos en Excel, CSV o web. Antes de usarlos hay que limpiar SKU, EAN, precios, disponibilidad e imágenes.',
                    'en' => 'Suppliers send catalogs as Excel, CSV or web sources. Before use they need SKU, EAN, price, availability and image cleanup.',
                ],
                'problem' => [
                    'es' => 'El procesamiento manual de catálogos generaba errores de SKU/EAN, precios inconsistentes y retrasos al preparar exportaciones útiles para el ERP u otros sistemas.',
                    'en' => 'Manual catalog processing caused SKU/EAN errors, inconsistent prices and delays preparing useful exports for the ERP or other systems.',
                ],
                'responsibilities' => [
                    'es' => 'Diseño del pipeline de importación, reglas de validación, manejo de errores y exportación.',
                    'en' => 'Import pipeline design, validation rules, error handling and export.',
                ],
                'role' => [
                    'es' => 'Automatización y procesamiento de datos',
                    'en' => 'Automation and data processing',
                ],
                'solution' => [
                    'es' => 'Procesamiento de SKU, EAN/UPC/GTIN, precios, disponibilidad e imágenes, con validación, registro de errores y exportación.',
                    'en' => 'Processing of SKU, EAN/UPC/GTIN, prices, availability and images, with validation, error logging and export.',
                ],
                'workflow_steps' => [
                    ['label' => 'Ingesta', 'description' => 'Excel, CSV o fuente web.'],
                    ['label' => 'Limpieza y normalización', 'description' => 'SKU, EAN, precios y disponibilidad.'],
                    ['label' => 'Validación', 'description' => 'Errores visibles y trazables.'],
                    ['label' => 'Enriquecimiento', 'description' => 'Imágenes y campos auxiliares cuando aplica.'],
                    ['label' => 'Exportación', 'description' => 'Salida lista para el siguiente sistema.'],
                ],
                'qualitative_results' => [
                    ['label' => 'Menos trabajo manual sobre Excel'],
                    ['label' => 'Validaciones repetibles'],
                    ['label' => 'Errores visibles antes de exportar'],
                ],
                'tech' => ['php', 'mysql'],
                'type' => 'Automatización',
            ],
        ];

        foreach ($projects as $i => $p) {
            $project = Project::updateOrCreate(['slug' => $p['slug']], [
                'project_category_id' => $cat[$p['category']]->id,
                'name' => $p['name'],
                'summary' => $p['summary'] ?? null,
                'outcome_headline' => $p['outcome_headline'] ?? null,
                'context' => $p['context'] ?? null,
                'problem' => $p['problem'] ?? null,
                'responsibilities' => $p['responsibilities'] ?? null,
                'role' => $p['role'] ?? null,
                'solution' => $p['solution'] ?? null,
                'workflow_steps' => $p['workflow_steps'] ?? null,
                'challenges' => $p['challenges'] ?? null,
                'qualitative_results' => $p['qualitative_results'] ?? null,
                'project_type' => $p['type'] ?? null,
                'period' => $p['period'] ?? null,
                'status' => 'published',
                'visibility' => 'public',
                'lifecycle' => $p['lifecycle'] ?? 'production',
                'is_ongoing' => $p['is_ongoing'] ?? false,
                'is_featured' => true,
                'is_case_study' => true,
                'is_archived' => false,
                'featured_size' => $p['size'],
                'sort' => $i,
                'year' => $p['year'] ?? 2025,
                'published_at' => now(),
            ]);
            $ids = collect($p['tech'])->map(fn ($slug) => $tech[$slug]->id ?? null)->filter()->all();
            $project->technologies()->sync($ids);
            $project->recalculateCompleteness();
            $project->saveQuietly();
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
                'lifecycle' => 'historical',
                'is_featured' => false,
                'is_case_study' => false,
                'is_archived' => true,
                'sort' => 100 + $i,
                'published_at' => now(),
            ]);
            $ids = collect($techSlugs)->map(fn ($slug) => $tech[$slug]->id ?? null)->filter()->all();
            $project->technologies()->sync($ids);
        }
    }

    protected function seedExperience(): void
    {
        // Seed content mirrors confirmed CV/years. Rebuilt cleanly on reseed.
        Experience::query()->delete();

        $experiences = [
            [
                'role' => [
                    'es' => 'Informático · Desarrollo de software y sistemas internos',
                    'en' => 'IT Specialist · Software Development and Internal Systems',
                ],
                'company' => 'Musical Princesa',
                'company_url' => 'https://tienda.musicalprincesa.com',
                'location' => 'Madrid, España',
                'city' => 'Madrid',
                'country' => 'España',
                'description' => [
                    'es' => "Desarrollo y mantengo aplicaciones internas orientadas a mejorar la gestión de productos, proveedores, precios e inventario. Trabajo en la automatización de catálogos y en integraciones entre Dolibarr, PrestaShop y otros servicios utilizados por la empresa.\n\nTambién participo en la administración de servidores, repositorios privados, copias de seguridad y diagnóstico de incidencias, combinando desarrollo de software, infraestructura y mejora de procesos operativos.",
                    'en' => "I develop and maintain internal applications focused on improving product, supplier, pricing and inventory management. I work on catalogue automation and integrations between Dolibarr, PrestaShop and other services used by the company.\n\nI also contribute to server administration, private repositories, backups and incident diagnosis, combining software development, infrastructure and operational process improvement.",
                ],
                'start_date' => '2025',
                'end_date' => null,
                'is_current' => true,
                'modality' => null,
                'tech_tags' => ['PHP', 'Laravel', 'Dolibarr', 'PrestaShop', 'Linux', 'Docker'],
                'achievements' => [],
                'sort' => 0,
                'is_visible' => true,
                'is_featured' => true,
            ],
            [
                'role' => [
                    'es' => 'Cofundador y desarrollador web',
                    'en' => 'Co-founder and Web Developer',
                ],
                'company' => 'R&B Studio · Marketing digital',
                'company_sector' => 'Marketing digital',
                'company_url' => 'https://rbestudio.net',
                'location' => 'Ecuador',
                'city' => null,
                'country' => 'Ecuador',
                'description' => [
                    'es' => "Cofundé un estudio orientado al desarrollo web y la presencia digital de empresas y marcas. Me encargué del análisis de necesidades, configuración y desarrollo de sitios personalizados con WordPress, HTML5 y CSS3.\n\nTambién participé en la gestión técnica de dominios, alojamiento, contenidos y presencia digital de los clientes.",
                    'en' => "I co-founded a studio focused on web development and digital presence for businesses and brands. I was responsible for requirements analysis, configuration and development of customised websites using WordPress, HTML5 and CSS3.\n\nI also contributed to the technical management of domains, hosting, content and clients’ digital presence.",
                ],
                'start_date' => '2023',
                'end_date' => '2026',
                'is_current' => false,
                'modality' => 'side_project',
                'tech_tags' => ['WordPress', 'HTML5', 'CSS3'],
                'achievements' => [],
                'sort' => 1,
                'is_visible' => true,
                'is_featured' => false,
            ],
            [
                'role' => [
                    'es' => 'Desarrollador de software · Soporte técnico TIC',
                    'en' => 'Software Developer · IT Support',
                ],
                'company' => 'Instituto Superior Tecnológico Cruz Roja Ecuatoriana',
                'company_url' => 'https://www.cruzrojainstituto.edu.ec',
                'location' => 'Ecuador',
                'city' => null,
                'country' => 'Ecuador',
                'description' => [
                    'es' => "Desarrollé y mantuve sistemas web institucionales, bases de datos y herramientas utilizadas en procesos académicos y administrativos.\n\nTambién gestioné servidores, equipos y servicios del entorno Microsoft, presté soporte técnico y Help Desk, y administré la plataforma Moodle, incluyendo cursos, actividades, usuarios, docentes y estudiantes.\n\nEsta experiencia me permitió trabajar directamente con usuarios, comprender procesos institucionales y desarrollar soluciones adaptadas a necesidades operativas reales.",
                    'en' => "I developed and maintained institutional web systems, databases and tools used in academic and administrative processes.\n\nI also managed servers, equipment and Microsoft-based services, provided technical support and Help Desk assistance, and administered the Moodle platform, including courses, activities, users, lecturers and students.\n\nThis experience allowed me to work directly with users, understand institutional processes and develop solutions adapted to real operational needs.",
                ],
                'start_date' => '2019',
                'end_date' => '2025',
                'is_current' => false,
                'modality' => null,
                'tech_tags' => ['PHP', 'MySQL', 'Moodle', 'Microsoft 365', 'Linux'],
                'achievements' => [],
                'sort' => 2,
                'is_visible' => true,
                'is_featured' => false,
            ],
            [
                'role' => [
                    'es' => 'Becario de desarrollo de software',
                    'en' => 'Software Development Intern',
                ],
                'company' => 'Algoritmun',
                'company_url' => 'https://www.algoritmun.com',
                'location' => 'Ecuador',
                'city' => null,
                'country' => 'Ecuador',
                'description' => [
                    'es' => "Participé en el desarrollo de aplicaciones móviles con React Native y en el mantenimiento de bases de datos y servidores Linux con CentOS y Ubuntu Server.\n\nColaboré en tareas de soporte técnico, configuración de infraestructura y desarrollo de soluciones a medida, adquiriendo experiencia práctica en aplicaciones, servidores y entornos de producción.",
                    'en' => "I contributed to the development of mobile applications using React Native and to the maintenance of databases and Linux servers running CentOS and Ubuntu Server.\n\nI collaborated on technical support, infrastructure configuration and custom software tasks, gaining practical experience with applications, servers and production environments.",
                ],
                'start_date' => '2017',
                'end_date' => '2019',
                'is_current' => false,
                'modality' => 'internship',
                'tech_tags' => ['React Native', 'Linux', 'CentOS', 'Ubuntu Server'],
                'achievements' => [],
                'sort' => 3,
                'is_visible' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($experiences as $exp) {
            Experience::create($exp);
        }
    }

    protected function seedEducation(): void
    {
        Education::query()->delete();

        Education::create([
            'title' => ['es' => 'Tecnólogo en Desarrollo de Software', 'en' => 'Software Development Technologist'],
            'institution' => 'Instituto Superior Tecnológico de Turismo y Patrimonio Yavirac',
            'description' => [
                'es' => 'Formación en desarrollo de software, bases de datos y desarrollo de aplicaciones para entornos empresariales.',
                'en' => 'Training in software development, databases and application development for business environments.',
            ],
            'start_year' => '2017',
            'end_year' => '2019',
            'sort' => 0,
        ]);
    }
}
