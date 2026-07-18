<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('company'),
                TextInput::make('country'),
                TextInput::make('subject'),
                Textarea::make('message')
                    ->columnSpanFull(),
                TextInput::make('source')
                    ->required()
                    ->default('website'),
                TextInput::make('status')
                    ->required()
                    ->default('new'),
                TextInput::make('need_type'),
                TextInput::make('estimated_value'),
                TextInput::make('assigned_to')
                    ->numeric(),
                DateTimePicker::make('next_follow_up_at'),
                DateTimePicker::make('contacted_at'),
                DateTimePicker::make('closed_at'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
