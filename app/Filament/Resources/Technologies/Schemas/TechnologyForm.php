<?php

namespace App\Filament\Resources\Technologies\Schemas;

use App\Filament\Support\Translatable as T;
use App\Support\TechnologyTaxonomy;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TechnologyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identidad')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set, $get) => blank($get('slug'))
                            ? $set('slug', Str::slug((string) $state))
                            : null),
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Usado en filtros: /proyectos?tecnologia=slug'),
                    Select::make('area')
                        ->label('Categoría')
                        ->options(fn () => TechnologyTaxonomy::areaOptions('es'))
                        ->required()
                        ->native(false),
                    Select::make('relevance')
                        ->label('Relevancia')
                        ->options(fn () => TechnologyTaxonomy::relevanceOptions('es'))
                        ->required()
                        ->default('practical')
                        ->native(false)
                        ->helperText('No es un nivel de dominio. Indica si forma parte del trabajo actual.'),
                ])
                ->columns(2),

            Section::make('Descripción pública (opcional)')
                ->schema([
                    T::area('description', 'Descripción (idioma admin)', 3)
                        ->helperText('Solo para tecnologías principales. Déjala vacía si no aporta contexto.'),
                    TextInput::make('official_url')
                        ->label('URL oficial')
                        ->url()
                        ->columnSpanFull(),
                ]),

            Section::make('Presentación')
                ->schema([
                    FileUpload::make('icon_path')
                        ->label('Icono (opcional)')
                        ->disk('public')
                        ->directory('technologies')
                        ->image()
                        ->imageResizeMode('cover')
                        ->maxSize(1024),
                    TextInput::make('sort')->label('Orden')->numeric()->default(0),
                    DatePicker::make('last_used_on')->label('Última utilización (opcional)'),
                    Toggle::make('is_visible')->label('Visible')->default(true),
                    Toggle::make('is_featured')->label('Destacada')->default(false),
                    Toggle::make('show_on_about')->label('Mostrar en Sobre mí')->default(true),
                    Toggle::make('show_on_projects')->label('Mostrar en filtros de proyectos')->default(true),
                ])
                ->columns(2),
        ]);
    }
}
