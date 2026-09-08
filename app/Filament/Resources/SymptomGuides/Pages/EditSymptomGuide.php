<?php

namespace App\Filament\Resources\SymptomGuides\Pages;

use App\Filament\Resources\SymptomGuides\SymptomGuideResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSymptomGuide extends EditRecord
{
    protected static string $resource = SymptomGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
