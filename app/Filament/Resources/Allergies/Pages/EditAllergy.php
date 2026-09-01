<?php

namespace App\Filament\Resources\Allergies\Pages;

use App\Filament\Resources\Allergies\AllergyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAllergy extends EditRecord
{
    protected static string $resource = AllergyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
