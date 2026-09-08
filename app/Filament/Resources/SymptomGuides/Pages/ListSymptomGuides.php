<?php

namespace App\Filament\Resources\SymptomGuides\Pages;

use App\Filament\Resources\SymptomGuides\SymptomGuideResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSymptomGuides extends ListRecords
{
    protected static string $resource = SymptomGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
