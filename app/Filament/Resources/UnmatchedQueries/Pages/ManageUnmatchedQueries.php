<?php

namespace App\Filament\Resources\UnmatchedQueries\Pages;

use App\Filament\Resources\UnmatchedQueries\UnmatchedQueryResource;
use Filament\Resources\Pages\ManageRecords;

class ManageUnmatchedQueries extends ManageRecords
{
    protected static string $resource = UnmatchedQueryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
