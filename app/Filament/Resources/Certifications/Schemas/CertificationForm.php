<?php

namespace App\Filament\Resources\Certifications\Schemas;

use App\Filament\Support\Translatable as T;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CertificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            T::text('name', 'Nombre')->required(),
            TextInput::make('issuer')->label('Emisor'),
            TextInput::make('issued_at')->label('Fecha / Año'),
            TextInput::make('credential_url')->label('URL credencial')->url(),
            TextInput::make('sort')->label('Orden')->numeric()->default(0),
        ]);
    }
}
