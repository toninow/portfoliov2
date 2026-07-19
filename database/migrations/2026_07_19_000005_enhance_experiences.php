<?php

use App\Models\Experience;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('company_sector')->nullable()->after('company');
            $table->string('city')->nullable()->after('location');
            $table->string('country')->nullable()->after('city');
            $table->string('modality')->nullable()->after('is_current');
            $table->json('tech_tags')->nullable()->after('description');
            $table->json('achievements')->nullable()->after('tech_tags');
            $table->boolean('is_visible')->default(true)->after('sort');
            $table->boolean('is_featured')->default(false)->after('is_visible');
        });

        $this->refreshExperienceCopy();
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn([
                'company_sector',
                'city',
                'country',
                'modality',
                'tech_tags',
                'achievements',
                'is_visible',
                'is_featured',
            ]);
        });
    }

    protected function refreshExperienceCopy(): void
    {
        $rows = [
            'Musical Princesa' => [
                'role' => [
                    'es' => 'Informático · Desarrollo de software y sistemas internos',
                    'en' => 'IT Specialist · Software Development and Internal Systems',
                ],
                'company' => 'Musical Princesa',
                'company_sector' => null,
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
            'R&B Studio · Marketing Digital' => [
                'role' => [
                    'es' => 'Cofundador y desarrollador web',
                    'en' => 'Co-founder and Web Developer',
                ],
                'company' => 'R&B Studio · Marketing digital',
                'company_sector' => 'Marketing digital',
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
                // Overlaps with Instituto (and later Musical Princesa) in stored years.
                'modality' => 'side_project',
                'tech_tags' => ['WordPress', 'HTML5', 'CSS3'],
                'achievements' => [],
                'sort' => 1,
                'is_visible' => true,
                'is_featured' => false,
            ],
            'Instituto Superior Tecnológico Cruz Roja Ecuatoriana' => [
                'role' => [
                    'es' => 'Desarrollador de software · Soporte técnico TIC',
                    'en' => 'Software Developer · IT Support',
                ],
                'company' => 'Instituto Superior Tecnológico Cruz Roja Ecuatoriana',
                'company_sector' => null,
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
            'Algoritmun' => [
                'role' => [
                    'es' => 'Becario de desarrollo de software',
                    'en' => 'Software Development Intern',
                ],
                'company' => 'Algoritmun',
                'company_sector' => null,
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

        // Also match company renamed mid-update.
        $aliases = [
            'R&B Studio · Marketing digital' => 'R&B Studio · Marketing Digital',
        ];

        foreach ($rows as $company => $data) {
            $query = Experience::query()->where('company', $company);
            if (! $query->exists() && isset($aliases[$company])) {
                $query = Experience::query()->where('company', $aliases[$company]);
            }
            // Fallback: match by company prefix for Musical / Algoritmun / Instituto.
            if (! $query->exists()) {
                $query = Experience::query()->where('company', 'like', explode(' · ', $company)[0].'%');
            }

            $experience = $query->orderBy('sort')->first();
            if (! $experience) {
                continue;
            }

            foreach ($data as $key => $value) {
                if (in_array($key, ['role', 'description'], true) && is_array($value)) {
                    foreach ($value as $locale => $text) {
                        $experience->setTranslation($key, $locale, $text);
                    }

                    continue;
                }
                $experience->{$key} = $value;
            }
            $experience->save();
        }
    }
};
