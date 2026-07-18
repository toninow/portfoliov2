<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Filament\Resources\Leads\Tables\LeadsTable;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mensaje recibido')
                    ->description('Datos que envió la persona desde la web.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel(),
                        TextInput::make('company')
                            ->label('Empresa'),
                        TextInput::make('subject')
                            ->label('Asunto')
                            ->columnSpanFull(),
                        Textarea::make('message')
                            ->label('Mensaje')
                            ->rows(6)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Gestión (CRM)')
                    ->description('Solo tú ves esto. Sirve para hacer seguimiento del contacto.')
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options(LeadsTable::STATUS_LABELS)
                            ->default('new')
                            ->required(),
                        Select::make('assigned_to')
                            ->label('Asignado a')
                            ->options(fn () => User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->placeholder('Sin asignar'),
                        DateTimePicker::make('contacted_at')
                            ->label('Contactado el'),
                        DateTimePicker::make('next_follow_up_at')
                            ->label('Próximo seguimiento'),
                        Textarea::make('notes')
                            ->label('Notas internas')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
