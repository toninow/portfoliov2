<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Support\Translatable as T;
use App\Models\ProjectCategory;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                Step::make('Identidad')->schema([
                    T::text('name', 'Nombre')->required()->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true),
                    Select::make('project_category_id')->label('Categoría')
                        ->options(fn () => ProjectCategory::all()->mapWithKeys(fn ($c) => [$c->id => $c->getTranslation('name', 'es')]))
                        ->searchable(),
                    Select::make('status')->label('Estado')->required()->default('draft')
                        ->options(['draft' => 'Borrador', 'published' => 'Publicado', 'archived' => 'Archivado']),
                    Select::make('visibility')->label('Visibilidad')->required()->default('public')
                        ->options(['public' => 'Público', 'private_summary' => 'Privado con resumen', 'draft' => 'Borrador', 'archived' => 'Archivado']),
                    Toggle::make('is_featured')->label('Destacado'),
                    Select::make('featured_size')->label('Tamaño destacado')->default('compact')
                        ->options(['large' => 'Grande', 'medium' => 'Mediano', 'compact' => 'Compacto']),
                    TextInput::make('sort')->label('Orden')->numeric()->default(0),
                ])->columns(2),

                Step::make('Presentación')->schema([
                    T::area('summary', 'Resumen', 3),
                    T::area('description', 'Descripción', 5),
                    T::text('role', 'Rol'),
                    TextInput::make('period')->label('Periodo'),
                    T::area('context', 'Contexto', 3),
                ]),

                Step::make('Caso de estudio')->schema([
                    T::area('problem', 'Problema'),
                    T::area('constraints', 'Restricciones'),
                    T::area('solution', 'Solución'),
                    T::area('process', 'Proceso'),
                    T::area('decisions', 'Decisiones técnicas'),
                    T::area('result', 'Resultado'),
                    T::area('improvements', 'Próximas mejoras'),
                ]),

                Step::make('Tecnología')->schema([
                    Select::make('technologies')->label('Tecnologías')
                        ->relationship('technologies', 'name')->multiple()->preload()->searchable(),
                    TextInput::make('project_type')->label('Tipo de proyecto'),
                    TextInput::make('repository_url')->label('Repositorio')->url(),
                    TextInput::make('url')->label('URL pública')->url(),
                ])->columns(2),

                Step::make('Multimedia')->schema([
                    FileUpload::make('main_image_path')->label('Imagen principal')
                        ->image()->disk('public')->directory('projects')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                        ->maxSize(5120)->imageEditor(),
                ]),

                Step::make('Publicación')->schema([
                    DateTimePicker::make('published_at')->label('Fecha de publicación'),
                    TextInput::make('year')->label('Año')->numeric(),
                ])->columns(2),
            ])->columnSpanFull()->skippable(),
        ]);
    }
}
