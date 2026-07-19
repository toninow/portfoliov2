<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Support\Translatable as T;
use App\Models\Project;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            T::text('title', 'Título')->required(),
            T::area('summary', 'Resumen', 3),
            T::area('problems', 'Problema habitual (una por línea)', 4),
            T::area('includes', 'Qué hago (una por línea)', 4),
            T::area('use_cases', 'Qué incluye (una por línea)', 4),
            T::area('deliverables', 'Entregables (una por línea)', 4),
            Select::make('technologies')->label('Tecnologías')
                ->relationship('technologies', 'name')->multiple()->preload()->searchable(),
            Select::make('related_project_id')
                ->label('Caso de estudio relacionado')
                ->options(fn () => Project::query()->published()->caseStudies()->orderBy('sort')->get()
                    ->mapWithKeys(fn (Project $project) => [$project->id => $project->getTranslation('name', 'es')]))
                ->searchable()
                ->nullable(),
            TextInput::make('icon')->label('Icono'),
            TextInput::make('sort')->label('Orden')->numeric()->default(0),
            Toggle::make('is_published')->label('Publicado')->default(true),
        ]);
    }
}
