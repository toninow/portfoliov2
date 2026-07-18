<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Filament\Resources\Leads\Support\ReplyActions;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    /** @var array<string, string> */
    public const STATUS_LABELS = [
        'new' => 'Nuevo',
        'contacted' => 'Contactado',
        'conversation' => 'En conversación',
        'proposal_sent' => 'Propuesta enviada',
        'won' => 'Ganado',
        'lost' => 'Perdido',
        'archived' => 'Archivado',
    ];

    /** @var array<string, string> */
    public const STATUS_COLORS = [
        'new' => 'warning',
        'contacted' => 'info',
        'conversation' => 'info',
        'proposal_sent' => 'primary',
        'won' => 'success',
        'lost' => 'danger',
        'archived' => 'gray',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->weight('bold')
                    ->searchable()
                    ->description(fn ($record) => $record->company)
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->copyMessage('Email copiado')
                    ->searchable(),
                TextColumn::make('message')
                    ->label('Mensaje')
                    ->limit(70)
                    ->tooltip(fn ($record) => $record->message)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => self::STATUS_COLORS[$state] ?? 'gray')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Recibido')
                    ->dateTime('d/m/Y H:i')
                    ->since()
                    ->sortable(),
                TextColumn::make('next_follow_up_at')
                    ->label('Seguimiento')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('source')
                    ->label('Origen')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(self::STATUS_LABELS),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Leer'),
                    ReplyActions::send(),
                    ReplyActions::mailto(),
                    EditAction::make()->label('Gestionar'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Sin mensajes todavía')
            ->emptyStateDescription('Cuando alguien te escriba desde la web, su mensaje aparecerá aquí.')
            ->emptyStateIcon('heroicon-o-envelope');
    }
}
