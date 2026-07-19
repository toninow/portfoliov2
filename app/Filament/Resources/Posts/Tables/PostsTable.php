<?php

namespace App\Filament\Resources\Posts\Tables;

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

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image_path')
                    ->label('')
                    ->disk('public')
                    ->visibility('public')
                    ->square()
                    ->size(52)
                    ->extraImgAttributes(['class' => 'object-cover ring-1 ring-gray-200 dark:ring-white/10']),
                TextColumn::make('title')
                    ->label('Título')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('title', 'es'))
                    ->searchable(query: fn ($query, $search) => $query->where('title->es', 'like', "%{$search}%"))
                    ->weight('bold')
                    ->wrap(),
                TextColumn::make('topic')
                    ->label('Tema')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('topic', 'es') ?: '—')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'published',
                        'warning' => 'archived',
                    ])
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                        'archived' => 'Archivado',
                        default => $state,
                    }),
                IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean(),
                TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('sort')
                    ->label('Orden')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                        'archived' => 'Archivado',
                    ]),
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
