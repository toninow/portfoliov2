<?php

namespace App\Filament\Resources\Technologies\Tables;

use App\Support\TechnologyTaxonomy;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TechnologiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                TextColumn::make('area')->label('Categoría')
                    ->formatStateUsing(fn ($state) => TechnologyTaxonomy::areaLabel((string) $state, 'es'))
                    ->badge(),
                TextColumn::make('relevance')->label('Relevancia')
                    ->formatStateUsing(fn ($state) => TechnologyTaxonomy::relevanceLabel((string) $state, 'es'))
                    ->badge(),
                TextColumn::make('projects_count')->label('Proyectos')
                    ->counts('projects')
                    ->sortable(),
                IconColumn::make('is_visible')->label('Visible')->boolean(),
                IconColumn::make('show_on_about')->label('Sobre mí')->boolean(),
                TextColumn::make('sort')->numeric()->sortable(),
            ])
            ->filters([
                SelectFilter::make('area')
                    ->label('Categoría')
                    ->options(fn () => TechnologyTaxonomy::areaOptions('es')),
                SelectFilter::make('relevance')
                    ->label('Relevancia')
                    ->options(fn () => TechnologyTaxonomy::relevanceOptions('es')),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function ($record) {
                        if ($record->relevance === 'primary' && $record->projects()->count() === 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tecnología principal sin proyectos')
                                ->body('"'.$record->name.'" está marcada como uso principal pero no tiene proyectos relacionados.')
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
