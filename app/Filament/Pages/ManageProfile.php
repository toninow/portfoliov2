<?php

namespace App\Filament\Pages;

use App\Models\Profile;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $navigationLabel = 'Perfil';

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Perfil';

    protected string $view = 'filament.pages.manage-profile';

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void
    {
        $p = Profile::current();

        $this->form->fill([
            'name' => $p->name,
            'headline' => $p->getTranslation('headline', 'es'),
            'bio' => $p->getTranslation('bio', 'es'),
            'about_long' => $p->getTranslation('about_long', 'es'),
            'availability' => $p->getTranslation('availability', 'es'),
            'headline_en' => $p->getTranslation('headline', 'en'),
            'bio_en' => $p->getTranslation('bio', 'en'),
            'email' => $p->email,
            'whatsapp' => $p->whatsapp,
            'location' => $p->location,
            'cv_path' => $p->cv_path,
            'cv_enabled' => (bool) ($p->cv_enabled ?? true),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Presentación')
                    ->description('Textos en español que se muestran en la portada, "Sobre mí" y el pie.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('headline')
                            ->label('Titular / categoría')
                            ->helperText('Tu categoría profesional, p. ej. "Desarrollador de software full-stack".')
                            ->columnSpanFull(),
                        Textarea::make('bio')
                            ->label('Bio corta')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('about_long')
                            ->label('Sobre mí (texto largo)')
                            ->rows(8)
                            ->columnSpanFull(),
                        TextInput::make('availability')
                            ->label('Disponibilidad')
                            ->helperText('Se muestra junto al punto verde en la portada.'),
                    ])
                    ->columns(2),

                Section::make('Versión en inglés')
                    ->description('Traducciones principales (opcional).')
                    ->collapsed()
                    ->schema([
                        TextInput::make('headline_en')->label('Headline (EN)')->columnSpanFull(),
                        Textarea::make('bio_en')->label('Bio (EN)')->rows(3)->columnSpanFull(),
                    ]),

                Section::make('Contacto')
                    ->schema([
                        TextInput::make('email')->label('Email')->email(),
                        TextInput::make('whatsapp')->label('WhatsApp')->tel(),
                        TextInput::make('location')->label('Ubicación'),
                    ])
                    ->columns(3),

                Section::make('Currículum (CV)')
                    ->description('Sube tu CV en PDF y decide si el botón "Descargar CV" aparece en la web.')
                    ->schema([
                        Toggle::make('cv_enabled')
                            ->label('Mostrar botón "Descargar CV" en la web')
                            ->helperText('Desactívalo para ocultar el CV sin borrar el archivo.')
                            ->columnSpanFull(),
                        FileUpload::make('cv_path')
                            ->label('Archivo del CV (PDF)')
                            ->disk('public')
                            ->directory('cv')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->downloadable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $p = Profile::current();
        $p->name = $state['name'] ?? $p->name;
        $p->setTranslation('headline', 'es', (string) ($state['headline'] ?? ''));
        $p->setTranslation('bio', 'es', (string) ($state['bio'] ?? ''));
        $p->setTranslation('about_long', 'es', (string) ($state['about_long'] ?? ''));
        $p->setTranslation('availability', 'es', (string) ($state['availability'] ?? ''));

        if (! empty($state['headline_en'])) {
            $p->setTranslation('headline', 'en', (string) $state['headline_en']);
        }
        if (! empty($state['bio_en'])) {
            $p->setTranslation('bio', 'en', (string) $state['bio_en']);
        }

        $p->email = $state['email'] ?? null;
        $p->whatsapp = $state['whatsapp'] ?? null;
        $p->location = $state['location'] ?? null;
        $p->cv_path = $state['cv_path'] ?? null;
        $p->cv_enabled = (bool) ($state['cv_enabled'] ?? true);
        $p->save();

        Notification::make()
            ->success()
            ->title('Perfil actualizado')
            ->send();
    }
}
