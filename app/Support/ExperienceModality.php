<?php

namespace App\Support;

class ExperienceModality
{
    public const OPTIONS = [
        'full_time' => [
            'es' => 'Tiempo completo',
            'en' => 'Full time',
        ],
        'part_time' => [
            'es' => 'Tiempo parcial',
            'en' => 'Part time',
        ],
        'freelance' => [
            'es' => 'Freelance',
            'en' => 'Freelance',
        ],
        'side_project' => [
            'es' => 'Proyecto paralelo',
            'en' => 'Side project',
        ],
        'internship' => [
            'es' => 'Prácticas',
            'en' => 'Internship',
        ],
        'other' => [
            'es' => 'Otra',
            'en' => 'Other',
        ],
    ];

    /** @return array<string, string> */
    public static function options(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();

        return collect(self::OPTIONS)
            ->mapWithKeys(fn (array $labels, string $key) => [$key => $labels[$locale] ?? $labels['es']])
            ->all();
    }

    public static function label(?string $key, ?string $locale = null): ?string
    {
        if (! $key || ! isset(self::OPTIONS[$key])) {
            return null;
        }

        $locale = $locale ?: app()->getLocale();

        return self::OPTIONS[$key][$locale] ?? self::OPTIONS[$key]['es'];
    }
}
