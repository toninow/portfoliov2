<?php

use App\Models\Technology;
use App\Support\TechnologyTaxonomy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('technologies', function (Blueprint $table) {
            $table->string('relevance')->default('practical')->after('area');
            $table->json('description')->nullable()->after('icon_path');
            $table->boolean('is_visible')->default(true)->after('sort');
            $table->boolean('is_featured')->default(false)->after('is_visible');
            $table->boolean('show_on_about')->default(true)->after('is_featured');
            $table->boolean('show_on_projects')->default(true)->after('show_on_about');
            $table->string('official_url')->nullable()->after('show_on_projects');
            $table->date('last_used_on')->nullable()->after('official_url');
        });

        $this->classifyExisting();
    }

    public function down(): void
    {
        Schema::table('technologies', function (Blueprint $table) {
            $table->dropColumn([
                'relevance',
                'description',
                'is_visible',
                'is_featured',
                'show_on_about',
                'show_on_projects',
                'official_url',
                'last_used_on',
            ]);
        });
    }

    protected function classifyExisting(): void
    {
        // Map slug => [area, relevance, featured?, description es/en optional]
        $map = [
            'php' => ['backend', 'primary', true],
            'laravel' => ['backend', 'primary', true],
            'python' => ['backend', 'primary', true],
            'apis-rest' => ['backend', 'primary', true],
            'mysql' => ['data', 'primary', true],
            'postgresql' => ['data', 'primary', true],
            'sql-server' => ['data', 'practical', false],
            'livewire' => ['web', 'primary', true],
            'blade' => ['web', 'primary', true],
            'tailwind-css' => ['web', 'primary', true],
            'javascript' => ['web', 'primary', true],
            'html5' => ['web', 'practical', false],
            'css3' => ['web', 'practical', false],
            'linux' => ['infra', 'primary', true],
            'docker' => ['infra', 'primary', true],
            'git' => ['infra', 'primary', true],
            'apache' => ['infra', 'primary', true],
            'gitea' => ['infra', 'primary', true],
            'restic' => ['infra', 'primary', true],
            'dolibarr' => ['platforms', 'primary', true],
            'prestashop' => ['platforms', 'primary', true],
            'wordpress' => ['platforms', 'practical', false],
            'moodle' => ['platforms', 'practical', false],
            'bitrix' => ['platforms', 'previous', false],
            'java' => ['additional', 'previous', false],
            'spring-boot' => ['additional', 'previous', false],
            'django' => ['additional', 'previous', false],
            'power-fx' => ['additional', 'previous', false],
            'flutter' => ['additional', 'previous', false],
            'dart' => ['additional', 'previous', false],
            'react-native' => ['additional', 'previous', false],
            'bootstrap' => ['additional', 'previous', false],
            'microsoft-365' => ['tools', 'practical', false],
            'google-workspace' => ['tools', 'practical', false],
            'scrum' => ['tools', 'practical', false],
            'cursor' => ['tools', 'practical', false],
            'ia-gpt-claude' => ['tools', 'practical', false],
            'automatizacion-con-ia' => ['tools', 'practical', false],
        ];

        $descriptions = [
            'laravel' => [
                'es' => 'Utilizado en aplicaciones internas, paneles administrativos y procesamiento de datos.',
                'en' => 'Used in internal applications, admin panels and data processing.',
            ],
            'docker' => [
                'es' => 'Utilizado para desplegar y aislar servicios internos.',
                'en' => 'Used to deploy and isolate internal services.',
            ],
            'dolibarr' => [
                'es' => 'Utilizado en integraciones, consultas mediante API y herramientas de gestión empresarial.',
                'en' => 'Used in integrations, API queries and business management tools.',
            ],
            'php' => [
                'es' => 'Lenguaje principal para backend, automatizaciones e integraciones.',
                'en' => 'Main language for backend, automations and integrations.',
            ],
            'apis-rest' => [
                'es' => 'Integración entre sistemas, servicios internos y plataformas empresariales.',
                'en' => 'Integration between systems, internal services and business platforms.',
            ],
        ];

        // Rename AI entries to non-brand skill labels without deleting rows.
        Technology::query()->where('slug', 'ia-gpt-claude')->update([
            'name' => 'AI assistants',
        ]);
        Technology::query()->where('slug', 'automatizacion-con-ia')->update([
            'name' => 'AI-assisted automation',
        ]);

        foreach (Technology::query()->get() as $tech) {
            $slug = $tech->slug;
            // Legacy area remaps when not in explicit map.
            if (! isset($map[$slug])) {
                $area = match ($tech->area) {
                    'frontend' => 'web',
                    'erp' => 'platforms',
                    'ia' => 'tools',
                    default => in_array($tech->area, array_keys(TechnologyTaxonomy::AREAS), true)
                        ? $tech->area
                        : 'additional',
                };
                $tech->area = $area;
                $tech->relevance = 'previous';
                $tech->is_visible = true;
                $tech->show_on_about = true;
                $tech->show_on_projects = true;
                $tech->save();

                continue;
            }

            [$area, $relevance, $featured] = $map[$slug];
            $tech->area = $area;
            $tech->relevance = $relevance;
            $tech->is_featured = $featured;
            $tech->is_visible = true;
            $tech->show_on_about = true;
            $tech->show_on_projects = true;

            if (isset($descriptions[$slug])) {
                $tech->description = $descriptions[$slug];
            }

            $tech->save();
        }
    }
};
