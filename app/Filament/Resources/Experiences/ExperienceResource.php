<?php

namespace App\Filament\Resources\Experiences;

use App\Filament\Resources\Experiences\Pages\CreateExperience;
use App\Filament\Resources\Experiences\Pages\EditExperience;
use App\Filament\Resources\Experiences\Pages\ListExperiences;
use App\Filament\Resources\Experiences\Schemas\ExperienceForm;
use App\Filament\Resources\Experiences\Tables\ExperiencesTable;
use App\Models\Experience;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExperienceResource extends Resource
{
    protected static ?string $model = Experience::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?string $navigationLabel = 'Experiencia';

    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return ExperienceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExperiencesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExperiences::route('/'),
            'create' => CreateExperience::route('/create'),
            'edit' => EditExperience::route('/{record}/edit'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function mergeLocaleFields(array $data): array
    {
        $roleEn = $data['role_en'] ?? null;
        $descriptionEn = $data['description_en'] ?? null;
        unset($data['role_en'], $data['description_en']);

        $role = $data['role'] ?? [];
        if (! is_array($role)) {
            $role = ['es' => (string) $role];
        }
        if (filled($roleEn)) {
            $role['en'] = (string) $roleEn;
        }
        $data['role'] = $role;

        $description = $data['description'] ?? [];
        if (! is_array($description)) {
            $description = ['es' => (string) $description];
        }
        if (filled($descriptionEn)) {
            $description['en'] = (string) $descriptionEn;
        }
        $data['description'] = $description;

        if (! empty($data['is_current'])) {
            $data['end_date'] = null;
        }

        $displayLocation = collect([$data['city'] ?? null, $data['country'] ?? null])
            ->filter(fn ($part) => filled($part))
            ->unique()
            ->implode(', ');

        if ($displayLocation !== '') {
            $data['location'] = $displayLocation;
        }

        $data['is_visible'] = (bool) ($data['is_visible'] ?? true);
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);
        $data['achievements'] = array_values($data['achievements'] ?? []);
        $data['tech_tags'] = array_values(array_filter($data['tech_tags'] ?? []));

        return $data;
    }

    public static function warnMissingEnglish(?Experience $record): void
    {
        if (! $record) {
            return;
        }

        $missing = [];
        if (! filled($record->getTranslation('role', 'en', false))) {
            $missing[] = 'cargo EN';
        }
        if (! filled($record->getTranslation('description', 'en', false))) {
            $missing[] = 'descripción EN';
        }

        if ($missing === []) {
            return;
        }

        Notification::make()
            ->warning()
            ->title('Falta traducción inglesa')
            ->body('Completa: '.implode(', ', $missing).'.')
            ->send();
    }
}
