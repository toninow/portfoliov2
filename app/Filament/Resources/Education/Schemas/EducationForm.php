<?php

namespace App\Filament\Resources\Education\Schemas;

use App\Filament\Support\Translatable as T;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EducationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            T::text('title', 'Título / Programa')->required(),
            TextInput::make('institution')->label('Institución'),
            TextInput::make('institution_url')
                ->label('Web de la institución')
                ->url()
                ->placeholder('https://…'),
            T::area('description', 'Descripción', 3),
            TextInput::make('start_year')->label('Año inicio'),
            TextInput::make('end_year')->label('Año fin'),
            TextInput::make('sort')->label('Orden')->numeric()->default(0),
        ]);
    }
}
