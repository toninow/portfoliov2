<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Support\Translatable as T;
use App\Models\ProjectCategory;
use App\Models\ProjectImage;
use App\Support\ProjectLifecycle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Proyecto')
                ->persistTabInQueryString()
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Básica')->schema(self::basicTab()),
                    Tab::make('Historia')->schema(self::storyTab()),
                    Tab::make('Impacto')->schema(self::impactTab()),
                    Tab::make('Tecnología')->schema(self::techTab()),
                    Tab::make('Multimedia')->schema(self::mediaTab()),
                    Tab::make('SEO')->schema(self::seoTab()),
                ]),
        ]);
    }

    /** @return array<int, mixed> */
    protected static function basicTab(): array
    {
        return [
            Section::make('Identidad')->schema([
                T::text('name', 'Título')->required()->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set, $get) => blank($get('slug')) ? $set('slug', Str::slug((string) $state)) : null),
                TextInput::make('slug')->required()->unique(ignoreRecord: true)
                    ->helperText('URL pública. No cambiar si el proyecto ya está indexado.'),
                Select::make('project_category_id')->label('Categoría')
                    ->options(fn () => ProjectCategory::query()->orderBy('sort')->get()
                        ->mapWithKeys(fn ($c) => [$c->id => $c->getTranslation('name', 'es')]))
                    ->searchable(),
                TextInput::make('project_type')->label('Tipo de proyecto')
                    ->placeholder('Aplicación interna, Integración…'),
                Select::make('lifecycle')->label('Estado del trabajo')->required()
                    ->options(fn () => ProjectLifecycle::options('es'))
                    ->default('completed'),
                Select::make('status')->label('Publicación')->required()->default('draft')
                    ->options(['draft' => 'Borrador', 'published' => 'Publicado', 'archived' => 'Archivado'])
                    ->helperText('Controla si aparece en la web.'),
                Select::make('visibility')->label('Visibilidad')->required()->default('public')
                    ->options([
                        'public' => 'Público',
                        'private_summary' => 'Resumen público (confidencial)',
                        'draft' => 'Borrador',
                        'archived' => 'Archivado',
                    ]),
                Select::make('confidentiality_level')->label('Confidencialidad')->default('public')
                    ->options(['public' => 'Público', 'confidential' => 'Confidencial']),
            ])->columns(2),

            Section::make('Presentación y periodo')->schema([
                T::area('summary', 'Descripción corta', 3)->required(),
                T::text('outcome_headline', 'Resultado principal (una frase)')
                    ->helperText('Aparece en la tarjeta y el hero. Déjalo vacío si aún no hay resultado.'),
                T::text('role', 'Rol'),
                TextInput::make('period')->label('Periodo (texto)')->placeholder('2025 – Actualidad'),
                TextInput::make('year')->label('Año (filtro)')->numeric()
                    ->helperText('Se usa en los filtros. Generado dinámicamente en la web.'),
                Toggle::make('is_ongoing')->label('En curso'),
            ])->columns(2),

            Section::make('Clasificación y orden')->schema([
                Toggle::make('is_case_study')->label('Caso de estudio')->default(false)
                    ->helperText('Aparece en la sección principal de proyectos.'),
                Toggle::make('is_featured')->label('Destacado en portada'),
                Toggle::make('is_archived')->label('Archivo')->default(false)
                    ->helperText('Proyectos anteriores / web / landings.'),
                Select::make('featured_size')->label('Tamaño en portada')->default('compact')
                    ->options(['large' => 'Grande', 'medium' => 'Mediano', 'compact' => 'Compacto']),
                TextInput::make('sort')->label('Orden')->numeric()->default(0),
                TextInput::make('completeness_score')->label('Completitud %')->disabled()->dehydrated(false),
            ])->columns(3),
        ];
    }

    /** @return array<int, mixed> */
    protected static function storyTab(): array
    {
        return [
            T::area('context', 'Contexto', 4)
                ->helperText('Entorno, usuarios afectados, proceso anterior y sistemas involucrados.'),
            T::area('problem', 'El problema', 4),
            T::area('constraints', 'Restricciones', 3),
            T::area('responsibilities', 'Mi responsabilidad', 4)
                ->helperText('Qué hiciste tú concretamente (análisis, backend, UX, infra…).'),
            T::area('solution', 'La solución', 5),
            T::area('process', 'Proceso (texto libre)', 3)
                ->helperText('Opcional si ya usas el flujo estructurado en Impacto.'),
            T::area('result', 'Resultados', 4),
            T::area('learnings', 'Aprendizajes', 3),
            T::area('improvements', 'Próximas mejoras', 3),
        ];
    }

    /** @return array<int, mixed> */
    protected static function impactTab(): array
    {
        return [
            Repeater::make('metrics')->label('Métricas')->relationship()
                ->schema([
                    TextInput::make('value')->label('Valor')->required(),
                    TextInput::make('unit')->label('Unidad'),
                    TextInput::make('name')->label('Etiqueta')
                        ->formatStateUsing(fn ($state, $record) => is_array($state)
                            ? ($state[app()->getLocale()] ?? $state['es'] ?? '')
                            : ($record?->getTranslation('name', 'es') ?? (string) $state))
                        ->dehydrateStateUsing(fn ($state, $record) => array_merge(
                            $record?->getTranslations('name') ?? [],
                            [app()->getLocale() => $state]
                        ))
                        ->required(),
                    Textarea::make('description')->label('Descripción')->rows(2)
                        ->formatStateUsing(fn ($state, $record) => is_array($state)
                            ? ($state[app()->getLocale()] ?? $state['es'] ?? '')
                            : ($record?->getTranslation('description', 'es') ?? (string) $state))
                        ->dehydrateStateUsing(fn ($state, $record) => array_merge(
                            $record?->getTranslations('description') ?? [],
                            [app()->getLocale() => $state]
                        )),
                    Toggle::make('is_public')->label('Visible públicamente')->default(true),
                    Toggle::make('is_approximate')->label('Cifra aproximada')->default(false),
                    TextInput::make('sort')->label('Orden')->numeric()->default(0),
                ])
                ->orderColumn('sort')
                ->collapsible()
                ->reorderable()
                ->defaultItems(0)
                ->itemLabel(fn (array $state): ?string => is_string($state['name'] ?? null)
                    ? $state['name']
                    : (is_array($state['name'] ?? null) ? ($state['name']['es'] ?? null) : null))
                ->helperText('Solo se muestran en la web las métricas públicas con valor y etiqueta. No inventes cifras.'),

            Repeater::make('workflow_steps')->label('Flujo de funcionamiento')
                ->schema([
                    TextInput::make('label')->label('Paso')->required(),
                    Textarea::make('description')->label('Detalle')->rows(2),
                ])
                ->reorderable()
                ->collapsible()
                ->defaultItems(0)
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? null),

            Repeater::make('features')->label('Funcionalidades principales')
                ->schema([
                    TextInput::make('title')->label('Título')->required(),
                    Textarea::make('description')->label('Descripción')->rows(2),
                    TextInput::make('icon')->label('Icono (opcional)'),
                ])
                ->reorderable()->collapsible()->defaultItems(0)
                ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),

            Repeater::make('challenges')->label('Dificultades resueltas')
                ->schema([
                    Textarea::make('difficulty')->label('Dificultad')->rows(2)->required(),
                    Textarea::make('decision')->label('Decisión')->rows(2),
                    Textarea::make('outcome')->label('Resultado')->rows(2),
                ])
                ->reorderable()->collapsible()->defaultItems(0),

            Repeater::make('technical_decisions')->label('Decisiones técnicas')
                ->schema([
                    TextInput::make('decision')->label('Decisión')->required(),
                    Textarea::make('reason')->label('Motivo')->rows(2),
                    Textarea::make('alternatives')->label('Alternativas')->rows(2),
                    Textarea::make('benefit')->label('Beneficio')->rows(2),
                ])
                ->reorderable()->collapsible()->defaultItems(0),

            Repeater::make('qualitative_results')->label('Resultados cualitativos')
                ->schema([
                    TextInput::make('label')->label('Resultado')->required(),
                ])
                ->reorderable()->defaultItems(0),
        ];
    }

    /** @return array<int, mixed> */
    protected static function techTab(): array
    {
        return [
            Select::make('technologies')->label('Stack / tecnologías')
                ->relationship('technologies', 'name')->multiple()->preload()->searchable()
                ->helperText('Las primeras 4 aparecen en las tarjetas.'),
            T::area('architecture_description', 'Arquitectura y enfoque técnico', 5),
            T::area('decisions', 'Notas técnicas (texto libre)', 3),
            TextInput::make('repository_url')->label('Repositorio')->url(),
            TextInput::make('url')->label('URL pública')->url(),
            Repeater::make('external_links')->label('Enlaces externos')
                ->schema([
                    TextInput::make('label')->label('Etiqueta')->required(),
                    TextInput::make('url')->label('URL')->url()->required(),
                ])
                ->defaultItems(0)
                ->columns(2),
        ];
    }

    /** @return array<int, mixed> */
    protected static function mediaTab(): array
    {
        return [
            FileUpload::make('main_image_path')->label('Portada')
                ->image()->disk('public')->directory('projects')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                ->maxSize(5120)->imageEditor()
                ->helperText('Obligatoria para publicar un caso de estudio.'),
            Repeater::make('images')->label('Galería')->relationship()
                ->schema([
                    FileUpload::make('path')->label('Imagen')->image()->disk('public')
                        ->directory('projects/gallery')->required()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                        ->maxSize(5120),
                    TextInput::make('alt')->label('Texto alternativo')
                        ->formatStateUsing(fn ($state, $record) => is_array($state)
                            ? ($state[app()->getLocale()] ?? $state['es'] ?? '')
                            : ($record?->getTranslation('alt', 'es') ?? (string) $state))
                        ->dehydrateStateUsing(fn ($state, $record) => array_merge(
                            $record?->getTranslations('alt') ?? [],
                            [app()->getLocale() => $state]
                        ))
                        ->required(),
                    TextInput::make('caption')->label('Pie de imagen')
                        ->formatStateUsing(fn ($state, $record) => is_array($state)
                            ? ($state[app()->getLocale()] ?? $state['es'] ?? '')
                            : ($record?->getTranslation('caption', 'es') ?? (string) $state))
                        ->dehydrateStateUsing(fn ($state, $record) => array_merge(
                            $record?->getTranslations('caption') ?? [],
                            [app()->getLocale() => $state]
                        )),
                    Select::make('type')->label('Tipo')->default('gallery')
                        ->options(collect(ProjectImage::TYPES)->mapWithKeys(fn ($t) => [$t => ucfirst($t)])),
                    Toggle::make('is_featured')->label('Destacada'),
                    Toggle::make('is_visible')->label('Visible')->default(true),
                    TextInput::make('sort')->numeric()->default(0)->label('Orden'),
                ])
                ->orderColumn('sort')
                ->collapsible()
                ->reorderable()
                ->defaultItems(0),
        ];
    }

    /** @return array<int, mixed> */
    protected static function seoTab(): array
    {
        return [
            Section::make('SEO')->schema([
                TextInput::make('seo.title.es')->label('SEO título ES'),
                TextInput::make('seo.title.en')->label('SEO título EN'),
                Textarea::make('seo.description.es')->label('Meta description ES')->rows(2),
                Textarea::make('seo.description.en')->label('Meta description EN')->rows(2),
                Toggle::make('seo.indexable')->label('Indexable')->default(true),
            ])->columns(2),
            DateTimePicker::make('published_at')->label('Fecha de publicación'),
        ];
    }
}
