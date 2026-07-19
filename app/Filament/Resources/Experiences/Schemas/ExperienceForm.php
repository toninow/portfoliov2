<?php

namespace App\Filament\Resources\Experiences\Schemas;

use App\Filament\Support\Translatable as T;
use App\Support\ExperienceModality;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Puesto')
                ->description('El cargo en español es obligatorio. Completa también el inglés para /en.')
                ->schema([
                    T::text('role', 'Cargo (ES)')
                        ->required()
                        ->helperText('Ej.: Informático · Desarrollo de software y sistemas internos'),
                    TextInput::make('role_en')
                        ->label('Cargo (EN)')
                        ->helperText('Si falta, la web en inglés puede mostrar el cargo en español.'),
                    TextInput::make('company')->label('Empresa')->required(),
                    TextInput::make('company_sector')->label('Sector o descripción de empresa')
                        ->placeholder('Marketing digital, educación…'),
                    TextInput::make('company_url')->label('Web de la empresa')->url()->placeholder('https://…'),
                    TextInput::make('city')->label('Ciudad'),
                    TextInput::make('country')->label('País'),
                    TextInput::make('location')->label('Ubicación (legado)')
                        ->helperText('Respaldo si ciudad/país están vacíos.'),
                ])
                ->columns(2),

            Section::make('Periodo y modalidad')
                ->schema([
                    TextInput::make('start_date')->label('Inicio')->required()
                        ->placeholder('2025')
                        ->helperText('Usa meses solo si están confirmados.'),
                    TextInput::make('end_date')->label('Finalización')
                        ->placeholder('2026')
                        ->disabled(fn (Get $get): bool => (bool) $get('is_current'))
                        ->dehydrated(true)
                        ->rules([
                            fn (Get $get): \Closure => function (string $attribute, mixed $value, \Closure $fail) use ($get) {
                                if ($get('is_current') || blank($value) || blank($get('start_date'))) {
                                    return;
                                }
                                if ((string) $value < (string) $get('start_date')) {
                                    $fail('La fecha final no puede ser anterior a la inicial.');
                                }
                            },
                        ]),
                    Toggle::make('is_current')->label('Trabajo actual')
                        ->helperText('Activo = sin fecha final; en la web se muestra “2025 – Actualidad” (sin badge “Actual” extra).')
                        ->live()
                        ->afterStateUpdated(function (mixed $state, callable $set): void {
                            if ($state) {
                                $set('end_date', null);
                            }
                        }),
                    Select::make('modality')->label('Modalidad')
                        ->options(fn () => ExperienceModality::options('es'))
                        ->placeholder('Sin indicar')
                        ->helperText('Opcional. Solo si describe bien la situación real.'),
                ])
                ->columns(2),

            Section::make('Descripción')
                ->schema([
                    T::area('description', 'Descripción (ES)', 6)
                        ->required()
                        ->helperText('Separa párrafos con una línea en blanco.'),
                    Textarea::make('description_en')
                        ->label('Descripción (EN)')
                        ->rows(6),
                ]),

            Section::make('Tecnologías y logros')
                ->description('Opcional. Los logros sin título o no públicos no aparecen en la web.')
                ->schema([
                    TagsInput::make('tech_tags')
                        ->label('Tecnologías principales')
                        ->placeholder('Añadir tecnología')
                        ->reorderable(),
                    Repeater::make('achievements')
                        ->label('Logros')
                        ->schema([
                            TextInput::make('title')->label('Título')->required(),
                            Textarea::make('description')->label('Descripción')->rows(2),
                            TextInput::make('metric')->label('Métrica (opcional)')
                                ->helperText('Solo cifras confirmadas.'),
                            Toggle::make('is_public')->label('Visible públicamente')->default(false),
                            TextInput::make('sort')->label('Orden')->numeric()->default(0),
                        ])
                        ->collapsed()
                        ->defaultItems(0)
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                        ->reorderable()
                        ->columnSpanFull(),
                ]),

            Section::make('Publicación')
                ->schema([
                    TextInput::make('sort')->label('Orden')->numeric()->default(0)
                        ->helperText('Menor número = más arriba.'),
                    Toggle::make('is_visible')->label('Visible en la web')->default(true),
                    Toggle::make('is_featured')->label('Destacado')->default(false),
                ])
                ->columns(3),
        ]);
    }
}
