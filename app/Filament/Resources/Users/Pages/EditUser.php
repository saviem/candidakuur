<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action): void {
                    if (auth()->id() === $this->record->id) {
                        Notification::make()
                            ->title('Je kunt jezelf niet verwijderen')
                            ->danger()
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
