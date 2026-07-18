<?php

namespace App\Filament\Resources\HomepageSections\Schemas;

use App\Filament\Support\Translatable as T;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HomepageSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('key')->label('Clave')->required()
                ->helperText('Identificador interno de la sección (no cambiar salvo necesidad).'),
            T::text('title', 'Título'),
            T::area('subtitle', 'Subtítulo', 2),
            T::area('body', 'Contenido', 4),
            Toggle::make('is_visible')->label('Visible')->default(true),
            TextInput::make('sort')->label('Orden')->numeric()->default(0),
        ]);
    }
}
