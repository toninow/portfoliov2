<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\ProjectCategory;
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
                TextColumn::make('status')->label('Estado')->badge()
                    ->colors(['gray' => 'draft', 'success' => 'published', 'warning' => 'archived']),
                IconColumn::make('is_featured')->label('Destacado')->boolean(),
                TextColumn::make('year')->label('Año')->sortable(),
                TextColumn::make('sort')->label('Orden')->sortable()->toggleable(),
                TextColumn::make('published_at')->label('Publicado')->dateTime('d/m/Y')->sortable()->toggleable(),
            ])
            ->defaultSort('sort')
            ->filters([
                SelectFilter::make('status')->label('Estado')
                    ->options(['draft' => 'Borrador', 'published' => 'Publicado', 'archived' => 'Archivado']),
                SelectFilter::make('project_category_id')->label('Categoría')
                    ->options(fn () => ProjectCategory::all()->mapWithKeys(fn ($c) => [$c->id => $c->getTranslation('name', 'es')])),
                TernaryFilter::make('is_featured')->label('Destacado'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
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
