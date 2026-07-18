<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Filament\Resources\Leads\Tables\LeadsTable;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('El mensaje')
                    ->schema([
                        TextEntry::make('message')
                            ->label('')
                            ->prose()
                            ->columnSpanFull(),
                    ]),

                Section::make('Quién escribe')
                    ->schema([
                        TextEntry::make('name')->label('Nombre'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->copyable()
                            ->url(fn ($record) => 'mailto:'.$record->email),
                        TextEntry::make('phone')->label('Teléfono')->placeholder('—'),
                        TextEntry::make('company')->label('Empresa')->placeholder('—'),
                        TextEntry::make('country')->label('País')->placeholder('—'),
                        TextEntry::make('subject')->label('Asunto')->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('Estado')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => LeadsTable::STATUS_LABELS[$state] ?? $state)
                            ->color(fn (string $state): string => LeadsTable::STATUS_COLORS[$state] ?? 'gray'),
                        TextEntry::make('created_at')->label('Recibido')->dateTime('d/m/Y H:i'),
                        TextEntry::make('contacted_at')->label('Contactado')->dateTime('d/m/Y H:i')->placeholder('—'),
                        TextEntry::make('next_follow_up_at')->label('Próximo seguimiento')->dateTime('d/m/Y')->placeholder('—'),
                        TextEntry::make('notes')->label('Notas internas')->placeholder('—')->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
