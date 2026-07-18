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
            ->columns([
                TextColumn::make('role')->label('Rol')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('role', 'es'))
                    ->weight('bold'),
                TextColumn::make('company')
                    ->searchable(),
                TextColumn::make('location')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->searchable(),
                TextColumn::make('end_date')
                    ->searchable(),
                IconColumn::make('is_current')
                    ->boolean(),
                TextColumn::make('sort')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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
