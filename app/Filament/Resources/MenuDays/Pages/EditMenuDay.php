<?php

namespace App\Filament\Resources\MenuDays\Pages;

use App\Filament\Resources\MenuDays\MenuDayResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMenuDay extends EditRecord
{
    protected static string $resource = MenuDayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
