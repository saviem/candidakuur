<?php

namespace App\Filament\Resources\MenuDays\Pages;

use App\Filament\Resources\MenuDays\MenuDayResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMenuDays extends ListRecords
{
    protected static string $resource = MenuDayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
