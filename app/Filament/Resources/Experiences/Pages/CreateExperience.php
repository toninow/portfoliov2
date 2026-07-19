<?php

namespace App\Filament\Resources\Experiences\Pages;

use App\Filament\Resources\Experiences\ExperienceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExperience extends CreateRecord
{
    protected static string $resource = ExperienceResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return ExperienceResource::mergeLocaleFields($data);
    }

    protected function afterCreate(): void
    {
        ExperienceResource::warnMissingEnglish($this->record->fresh());
    }
}
