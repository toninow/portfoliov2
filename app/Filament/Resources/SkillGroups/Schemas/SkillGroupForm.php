<?php

namespace App\Filament\Resources\SkillGroups\Schemas;

use App\Filament\Support\Translatable as T;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SkillGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            T::text('name', 'Nombre del grupo')->required(),
            TextInput::make('sort')->label('Orden')->numeric()->default(0),
            Repeater::make('skills')->label('Habilidades')
                ->relationship()
                ->schema([
                    TextInput::make('name')->label('Nombre')->required(),
                    TextInput::make('sort')->label('Orden')->numeric()->default(0),
                ])
                ->orderColumn('sort')
                ->columns(2)
                ->columnSpanFull()
                ->reorderable(),
        ]);
    }
}
