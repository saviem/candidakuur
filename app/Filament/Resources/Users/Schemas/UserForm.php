<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Naam')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn (?string $state): string => Str::lower(trim((string) $state))),
                DateTimePicker::make('plus_until')
                    ->label('Plus tot')
                    ->native(false)
                    ->seconds(false)
                    ->helperText('Leeg = geen Plus. Of gebruik de actie "Plus activeren" in de lijst.'),
                Toggle::make('is_admin')
                    ->label('Admin')
                    ->helperText('Toegang tot dit adminpaneel.'),
            ]);
    }
}
