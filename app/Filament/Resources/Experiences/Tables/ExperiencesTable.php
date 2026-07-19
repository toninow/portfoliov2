<?php

namespace App\Filament\Resources\Experiences\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExperiencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('role')->label('Cargo')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('role', 'es'))
                    ->weight('bold')
                    ->searchable(query: function ($query, string $search) {
                        return $query->where('role', 'like', "%{$search}%");
                    }),
                TextColumn::make('company')->searchable(),
                TextColumn::make('location')->label('Ubicación')
                    ->formatStateUsing(fn ($record) => $record->displayLocation()),
                TextColumn::make('period')->label('Periodo')
                    ->state(fn ($record) => $record->periodLabel('es')),
                TextColumn::make('modality')->label('Modalidad')
                    ->formatStateUsing(fn ($record) => $record->modalityLabel('es') ?: '—'),
                IconColumn::make('is_current')->label('Actual')->boolean(),
                IconColumn::make('is_visible')->label('Visible')->boolean(),
                TextColumn::make('sort')->numeric()->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
