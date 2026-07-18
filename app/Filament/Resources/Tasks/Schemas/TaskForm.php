<?php

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('lead_id')
                    ->relationship('lead', 'name'),
                TextInput::make('assigned_to')
                    ->numeric(),
                DateTimePicker::make('due_at'),
                DateTimePicker::make('completed_at'),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('priority')
                    ->required()
                    ->default('normal'),
            ]);
    }
}
