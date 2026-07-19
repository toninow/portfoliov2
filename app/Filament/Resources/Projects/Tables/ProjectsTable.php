<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\ProjectCategory;
use App\Support\ProjectLifecycle;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('main_image_path')->label('')->square()->size(48),
                TextColumn::make('name')->label('Nombre')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', 'es'))
                    ->searchable(query: fn ($query, $search) => $query->where('name->es', 'like', "%{$search}%"))
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('name->es', $direction))
                    ->weight('bold'),
                TextColumn::make('category.id')->label('Categoría')
                    ->formatStateUsing(fn ($record) => $record->category?->getTranslation('name', 'es') ?? '—')
                    ->badge(),
                TextColumn::make('lifecycle')->label('Estado')
                    ->formatStateUsing(fn ($state) => ProjectLifecycle::label($state, 'es') ?? $state)
                    ->badge(),
                IconColumn::make('is_case_study')->label('Caso')->boolean(),
                IconColumn::make('is_featured')->label('Destacado')->boolean(),
                IconColumn::make('is_archived')->label('Archivo')->boolean()->toggleable(),
                TextColumn::make('completeness_score')->label('Completitud')
                    ->formatStateUsing(fn ($state) => ($state ?? 0).'%')
                    ->sortable()
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('status')->label('Publicación')->badge()
                    ->colors(['gray' => 'draft', 'success' => 'published', 'warning' => 'archived']),
                TextColumn::make('year')->label('Año')->sortable(),
                TextColumn::make('sort')->label('Orden')->sortable()->toggleable(),
                TextColumn::make('published_at')->label('Publicado')->dateTime('d/m/Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort')
            ->filters([
                SelectFilter::make('status')->label('Publicación')
                    ->options(['draft' => 'Borrador', 'published' => 'Publicado', 'archived' => 'Archivado']),
                SelectFilter::make('lifecycle')->label('Estado del trabajo')
                    ->options(fn () => ProjectLifecycle::options('es')),
                SelectFilter::make('project_category_id')->label('Categoría')
                    ->options(fn () => ProjectCategory::all()->mapWithKeys(fn ($c) => [$c->id => $c->getTranslation('name', 'es')])),
                TernaryFilter::make('is_case_study')->label('Caso de estudio'),
                TernaryFilter::make('is_featured')->label('Destacado'),
                TernaryFilter::make('is_archived')->label('Archivo'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('preview')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => url('/proyectos/'.$record->slug), shouldOpenInNewTab: true)
                    ->visible(fn ($record) => $record->isPubliclyVisible()),
                Action::make('togglePublish')
                    ->label(fn ($record) => $record->status === 'published' ? 'Despublicar' : 'Publicar')
                    ->icon('heroicon-o-globe-alt')
                    ->action(function ($record) {
                        $record->status = $record->status === 'published' ? 'draft' : 'published';
                        if ($record->status === 'published' && ! $record->published_at) {
                            $record->published_at = now();
                        }
                        $record->save();
                    }),
                Action::make('feature')
                    ->label(fn ($record) => $record->is_featured ? 'Quitar destacado' : 'Destacar')
                    ->icon('heroicon-o-star')
                    ->action(fn ($record) => $record->update(['is_featured' => ! $record->is_featured])),
                Action::make('archive')
                    ->label('Mover al archivo')
                    ->icon('heroicon-o-archive-box')
                    ->visible(fn ($record) => ! $record->is_archived)
                    ->action(fn ($record) => $record->update([
                        'is_archived' => true,
                        'is_case_study' => false,
                        'lifecycle' => 'historical',
                    ])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
