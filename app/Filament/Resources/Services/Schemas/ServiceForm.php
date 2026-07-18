<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Support\Translatable as T;
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
            T::area('problems', 'Problemas que resuelve (una por línea)', 4),
            T::area('includes', 'Qué incluye (una por línea)', 4),
            T::area('deliverables', 'Qué recibe el cliente (una por línea)', 4),
            T::area('use_cases', 'Casos de uso (una por línea)', 4),
            Select::make('technologies')->label('Tecnologías')
                ->relationship('technologies', 'name')->multiple()->preload()->searchable(),
            TextInput::make('icon')->label('Icono'),
            TextInput::make('sort')->label('Orden')->numeric()->default(0),
            Toggle::make('is_published')->label('Publicado')->default(true),
        ]);
    }
}
