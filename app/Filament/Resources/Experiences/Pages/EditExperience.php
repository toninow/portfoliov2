<?php

namespace App\Filament\Resources\Experiences\Pages;

use App\Filament\Resources\Experiences\ExperienceResource;
use App\Support\Locale;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExperience extends EditRecord
{
    protected static string $resource = ExperienceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Ver en la web')
                ->url(fn () => Locale::route('home').'#experiencia')
                ->openUrlInNewTab()
                ->color('gray'),
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['role_en'] = $this->record->getTranslation('role', 'en', false) ?: '';
        $data['description_en'] = $this->record->getTranslation('description', 'en', false) ?: '';

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return ExperienceResource::mergeLocaleFields($data);
    }

    protected function afterSave(): void
    {
        ExperienceResource::warnMissingEnglish($this->record->fresh());
    }
}
