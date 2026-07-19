<?php

namespace App\Support;

use App\Models\Project;

class ProjectLifecycle
{
    /** @return array<string, string> */
    public static function options(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        $labels = [
            'production' => ['es' => 'En producción', 'en' => 'In production'],
            'active_development' => ['es' => 'En desarrollo', 'en' => 'Active development'],
            'internal_pilot' => ['es' => 'Piloto interno', 'en' => 'Internal pilot'],
            'implementation' => ['es' => 'En implementación', 'en' => 'Implementation'],
            'completed' => ['es' => 'Completado', 'en' => 'Completed'],
            'historical' => ['es' => 'Histórico', 'en' => 'Historical'],
            'paused' => ['es' => 'En pausa', 'en' => 'Paused'],
        ];

        return collect(Project::LIFECYCLES)
            ->mapWithKeys(fn ($key) => [$key => $labels[$key][$locale] ?? $key])
            ->all();
    }

    public static function label(?string $lifecycle, ?string $locale = null): ?string
    {
        if (! $lifecycle) {
            return null;
        }

        return self::options($locale)[$lifecycle] ?? $lifecycle;
    }
}
