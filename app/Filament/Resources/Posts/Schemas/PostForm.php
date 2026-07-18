<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Support\Translatable as T;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contenido')
                    ->schema([
                        T::text('title', 'Título')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, ?string $old, $context) {
                                if ($context === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Se usa en la URL: /blog/mi-entrada')
                            ->columnSpanFull(),
                        T::text('topic', 'Tema')
                            ->helperText('Etiqueta corta, p. ej. "Cambio personal" o "Automatización".'),
                        T::area('excerpt', 'Resumen', 3)
                            ->helperText('Frase gancho que aparece en el listado.')
                            ->columnSpanFull(),
                        T::markdown('body', 'Contenido (Markdown)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Portada y publicación')
                    ->schema([
                        FileUpload::make('cover_image_path')
                            ->label('Imagen de portada')
                            ->image()
                            ->disk('public')
                            ->directory('blog')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                            ->maxSize(5120)
                            ->imageEditor()
                            ->columnSpanFull(),
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft' => 'Borrador',
                                'published' => 'Publicado',
                                'archived' => 'Archivado',
                            ])
                            ->default('draft')
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label('Fecha de publicación')
                            ->helperText('Se publica cuando el estado es "Publicado" y la fecha ya pasó.'),
                        Toggle::make('is_featured')
                            ->label('Destacado'),
                        TextInput::make('reading_minutes')
                            ->label('Minutos de lectura')
                            ->numeric()
                            ->helperText('Opcional. Si se deja vacío se calcula automáticamente.'),
                        TextInput::make('sort')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }
}
