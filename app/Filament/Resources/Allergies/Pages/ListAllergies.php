<?php

namespace App\Filament\Resources\Allergies\Pages;

use App\Filament\Resources\Allergies\AllergyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAllergies extends ListRecords
{
    protected static string $resource = AllergyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
