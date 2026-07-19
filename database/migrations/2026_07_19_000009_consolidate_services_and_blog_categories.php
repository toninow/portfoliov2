<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'related_project_id')) {
                $table->foreignId('related_project_id')
                    ->nullable()
                    ->after('is_published')
                    ->constrained('projects')
                    ->nullOnDelete();
            }
        });

        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'category')) {
                $table->string('category')->default('tech')->after('topic');
            }
        });

        // Hide legacy service rows; keep data and relations.
        DB::table('services')->update(['is_published' => false]);

        $projectIds = DB::table('projects')->pluck('id', 'slug');

        $canonical = [
            [
                'slug' => 'aplicaciones-internas-y-automatizacion',
                'sort' => 0,
                'related' => 'mp-proveedores',
                'title' => [
                    'es' => 'Aplicaciones internas y automatización',
                    'en' => 'Internal applications and automation',
                ],
                'summary' => [
                    'es' => 'Desarrollo de herramientas para sustituir hojas de cálculo, comprobaciones manuales y procesos repetitivos por flujos más claros y controlados.',
                    'en' => 'Building tools that replace spreadsheets, manual checks and repetitive work with clearer, controlled workflows.',
                ],
                'problems' => [
                    'es' => ['Procesos diarios en Excel o mensajes', 'Comprobaciones manuales propensas a error', 'Falta de trazabilidad operativa'],
                    'en' => ['Daily work stuck in spreadsheets or chat', 'Error-prone manual checks', 'No operational traceability'],
                ],
                'includes' => [
                    'es' => ['Análisis del proceso real', 'Diseño de la herramienta interna', 'Automatización e integración con sistemas existentes', 'Uso de IA como apoyo cuando aporta valor'],
                    'en' => ['Analysis of the real process', 'Internal tool design', 'Automation and integration with existing systems', 'AI assistants as support when they add value'],
                ],
                'deliverables' => [
                    'es' => ['Aplicación o flujo operativo', 'Documentación esencial', 'Despliegue y acompañamiento inicial'],
                    'en' => ['Application or operational flow', 'Essential documentation', 'Deployment and initial support'],
                ],
                'use_cases' => [
                    'es' => ['Paneles internos', 'Validación de catálogos', 'Flujos de revisión humana'],
                    'en' => ['Internal panels', 'Catalog validation', 'Human review workflows'],
                ],
            ],
            [
                'slug' => 'integraciones-empresariales',
                'sort' => 1,
                'related' => 'integracion-prestashop-dolibarr',
                'title' => [
                    'es' => 'Integraciones empresariales',
                    'en' => 'Business integrations',
                ],
                'summary' => [
                    'es' => 'Conexión entre ERP, ecommerce, proveedores, inventario, bases de datos y APIs para evitar información aislada o duplicada.',
                    'en' => 'Connecting ERP, ecommerce, suppliers, inventory, databases and APIs so information is not siloed or duplicated.',
                ],
                'problems' => [
                    'es' => ['Stock o precios descuadrados entre sistemas', 'Doble introducción de datos', 'Fallos de sincronización sin diagnóstico'],
                    'en' => ['Stock or prices out of sync across systems', 'Duplicate data entry', 'Sync failures without clear diagnosis'],
                ],
                'includes' => [
                    'es' => ['Diseño de la sincronización', 'APIs REST y reglas de dirección de datos', 'Manejo de errores y compatibilidad entre versiones'],
                    'en' => ['Sync design', 'REST APIs and data-direction rules', 'Error handling and version compatibility'],
                ],
                'deliverables' => [
                    'es' => ['Integración operativa', 'Registros o logs de diagnóstico', 'Guía de operación'],
                    'en' => ['Working integration', 'Diagnostic logs', 'Operations guide'],
                ],
                'use_cases' => [
                    'es' => ['PrestaShop ↔ Dolibarr', 'Proveedores ↔ ERP', 'Inventario consultable'],
                    'en' => ['PrestaShop ↔ Dolibarr', 'Suppliers ↔ ERP', 'Queryable inventory'],
                ],
            ],
            [
                'slug' => 'datos-y-catalogos',
                'sort' => 2,
                'related' => 'automatizacion-catalogos-proveedores',
                'title' => [
                    'es' => 'Datos y catálogos',
                    'en' => 'Data and catalogs',
                ],
                'summary' => [
                    'es' => 'Importación, normalización, validación y enriquecimiento de productos, referencias, códigos de barras, precios, stock e imágenes.',
                    'en' => 'Importing, normalizing, validating and enriching products, references, barcodes, prices, stock and images.',
                ],
                'problems' => [
                    'es' => ['Catálogos inconsistentes entre proveedores', 'EAN usados como identificador único incorrecto', 'Imágenes o precios incompletos'],
                    'en' => ['Inconsistent supplier catalogs', 'EANs misused as unique identifiers', 'Incomplete images or prices'],
                ],
                'includes' => [
                    'es' => ['Importaciones idempotentes', 'Matching y revisión humana', 'Validación de códigos y relaciones'],
                    'en' => ['Idempotent imports', 'Matching and human review', 'Code and relationship validation'],
                ],
                'deliverables' => [
                    'es' => ['Pipeline de datos', 'Informes de conflictos', 'Actualización controlada en destino'],
                    'en' => ['Data pipeline', 'Conflict reports', 'Controlled updates in the target system'],
                ],
                'use_cases' => [
                    'es' => ['Catálogos de proveedores', 'Enriquecimiento de productos', 'Control de stock consultable'],
                    'en' => ['Supplier catalogs', 'Product enrichment', 'Queryable stock control'],
                ],
            ],
            [
                'slug' => 'infraestructura-y-continuidad',
                'sort' => 3,
                'related' => 'gitea-autogestionado',
                'title' => [
                    'es' => 'Infraestructura y continuidad',
                    'en' => 'Infrastructure and continuity',
                ],
                'summary' => [
                    'es' => 'Administración de servicios Linux, despliegues, repositorios privados, copias de seguridad y diagnóstico de incidencias.',
                    'en' => 'Managing Linux services, deployments, private repositories, backups and incident diagnosis.',
                ],
                'problems' => [
                    'es' => ['Servicios internos frágiles', 'Backups sin restauración probada', 'Repositorios o despliegues poco controlados'],
                    'en' => ['Fragile internal services', 'Backups without tested restores', 'Uncontrolled repos or deployments'],
                ],
                'includes' => [
                    'es' => ['Servidores Linux y Docker', 'Gitea y flujos de despliegue', 'Restic y verificación de restauración', 'Diagnóstico de incidencias'],
                    'en' => ['Linux servers and Docker', 'Gitea and deployment flows', 'Restic and restore verification', 'Incident diagnosis'],
                ],
                'deliverables' => [
                    'es' => ['Servicio estable', 'Procedimiento de backup/restore', 'Documentación operativa'],
                    'en' => ['Stable service', 'Backup/restore procedure', 'Operational documentation'],
                ],
                'use_cases' => [
                    'es' => ['Gitea autogestionado', 'Backups con Restic', 'Servicios internos en Linux'],
                    'en' => ['Self-hosted Gitea', 'Restic backups', 'Internal Linux services'],
                ],
            ],
        ];

        foreach ($canonical as $service) {
            $relatedId = $projectIds[$service['related']] ?? null;

            DB::table('services')->updateOrInsert(
                ['slug' => $service['slug']],
                [
                    'title' => json_encode($service['title'], JSON_UNESCAPED_UNICODE),
                    'summary' => json_encode($service['summary'], JSON_UNESCAPED_UNICODE),
                    'problems' => json_encode($service['problems'], JSON_UNESCAPED_UNICODE),
                    'includes' => json_encode($service['includes'], JSON_UNESCAPED_UNICODE),
                    'deliverables' => json_encode($service['deliverables'], JSON_UNESCAPED_UNICODE),
                    'use_cases' => json_encode($service['use_cases'], JSON_UNESCAPED_UNICODE),
                    'sort' => $service['sort'],
                    'is_published' => true,
                    'related_project_id' => $relatedId,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // Work tools should not dominate project filters.
        if (Schema::hasColumn('technologies', 'show_on_projects')) {
            DB::table('technologies')
                ->where('area', 'tools')
                ->update(['show_on_projects' => false]);
        }

        // Prefer LinkedIn / GitHub in public footers; keep others inactive by default if present.
        if (Schema::hasTable('social_links')) {
            DB::table('social_links')
                ->whereIn('platform', ['twitter', 'instagram', 'facebook'])
                ->update(['is_active' => false]);
        }
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'related_project_id')) {
                $table->dropConstrainedForeignId('related_project_id');
            }
        });

        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
