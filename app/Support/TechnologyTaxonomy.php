<?php

namespace App\Support;

class TechnologyTaxonomy
{
    public const RELEVANCE = [
        'primary' => [
            'es' => 'Uso principal',
            'en' => 'Main Stack',
        ],
        'practical' => [
            'es' => 'Experiencia práctica',
            'en' => 'Practical Experience',
        ],
        'previous' => [
            'es' => 'Experiencia anterior',
            'en' => 'Previous Experience',
        ],
    ];

    public const AREAS = [
        'backend' => [
            'es' => 'Backend y automatización',
            'en' => 'Backend and Automation',
            'description' => [
                'es' => 'Backend de aplicaciones internas, automatización de procesos, integración de servicios y tratamiento de datos.',
                'en' => 'Backend for internal applications, process automation, service integration and data processing.',
            ],
        ],
        'data' => [
            'es' => 'Datos',
            'en' => 'Data',
            'description' => [
                'es' => 'Modelado, consulta, validación y mantenimiento de datos utilizados por aplicaciones y procesos empresariales.',
                'en' => 'Modelling, querying, validation and maintenance of data used by applications and business processes.',
            ],
        ],
        'web' => [
            'es' => 'Aplicaciones web',
            'en' => 'Web Applications',
            'description' => [
                'es' => 'Interfaces administrables y responsive para herramientas internas, paneles y procesos operativos.',
                'en' => 'Admin and responsive interfaces for internal tools, panels and operational processes.',
            ],
        ],
        'platforms' => [
            'es' => 'Plataformas e integraciones',
            'en' => 'Platforms and Integrations',
            'description' => [
                'es' => 'Sistemas con los que he desarrollado aplicaciones, integraciones, automatizaciones o infraestructura interna.',
                'en' => 'Systems for which I have developed applications, integrations, automations or internal infrastructure.',
            ],
        ],
        'infra' => [
            'es' => 'Infraestructura',
            'en' => 'Infrastructure',
            'description' => [
                'es' => 'Servidores, despliegues, repositorios privados, servicios internos y copias de seguridad.',
                'en' => 'Servers, deployments, private repositories, internal services and backups.',
            ],
        ],
        'tools' => [
            'es' => 'Herramientas de trabajo',
            'en' => 'Work Tools',
            'description' => [
                'es' => 'Herramientas que utilizo como apoyo para analizar, desarrollar, revisar y documentar soluciones.',
                'en' => 'Tools I use to support analysis, development, review and documentation.',
            ],
        ],
        'additional' => [
            'es' => 'Experiencia tecnológica adicional',
            'en' => 'Additional Technical Experience',
            'description' => [
                'es' => 'Otras tecnologías y plataformas utilizadas en proyectos profesionales, educativos o etapas anteriores de mi trayectoria.',
                'en' => 'Other technologies and platforms used in professional projects, educational work or earlier stages of my career.',
            ],
        ],
    ];

    /** Areas shown inside the main stack level. */
    public const PRIMARY_STACK_AREAS = ['backend', 'data', 'web', 'infra'];

    /** @return array<string, string> */
    public static function areaOptions(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();

        return collect(self::AREAS)
            ->mapWithKeys(fn (array $meta, string $key) => [$key => $meta[$locale] ?? $meta['es']])
            ->all();
    }

    /** @return array<string, string> */
    public static function relevanceOptions(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();

        return collect(self::RELEVANCE)
            ->mapWithKeys(fn (array $labels, string $key) => [$key => $labels[$locale] ?? $labels['es']])
            ->all();
    }

    public static function areaLabel(string $area, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return self::AREAS[$area][$locale] ?? self::AREAS[$area]['es'] ?? $area;
    }

    public static function areaDescription(string $area, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return self::AREAS[$area]['description'][$locale]
            ?? self::AREAS[$area]['description']['es']
            ?? '';
    }

    public static function relevanceLabel(string $relevance, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return self::RELEVANCE[$relevance][$locale] ?? self::RELEVANCE[$relevance]['es'] ?? $relevance;
    }
}
