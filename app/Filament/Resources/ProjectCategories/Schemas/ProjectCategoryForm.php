<?php

namespace App\Filament\Resources\ProjectCategories\Schemas;

use App\Filament\Support\Translatable as T;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            T::text('name', 'Nombre')->required(),
            TextInput::make('slug')->required()->unique(ignoreRecord: true),
            T::area('description', 'Descripción', 3),
            TextInput::make('sort')->label('Orden')->numeric()->default(0),
        ]);
    }
}
