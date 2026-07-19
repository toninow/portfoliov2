<?php

namespace App\Filament\Resources\Experiences\Schemas;

use App\Filament\Support\Translatable as T;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            T::text('role', 'Rol / Puesto')->required(),
            TextInput::make('company')->label('Empresa'),
            TextInput::make('company_url')->label('Web de la empresa')->url()->placeholder('https://…'),
            TextInput::make('location')->label('Ubicación'),
            T::area('description', 'Descripción', 4),
            TextInput::make('start_date')->label('Inicio (ej. 2022)'),
            TextInput::make('end_date')->label('Fin (ej. 2024)'),
            Toggle::make('is_current')->label('Actual'),
            TextInput::make('sort')->label('Orden')->numeric()->default(0),
        ]);
    }
}
