<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(64)
                    ->dehydrateStateUsing(fn (?string $state): string => Str::upper(trim((string) $state)))
                    ->helperText('Wordt automatisch omgezet naar hoofdletters.'),
                TextInput::make('label')
                    ->label('Label')
                    ->maxLength(255)
                    ->helperText('Optioneel, alleen voor jezelf (bijv. “Proefweek Instagram”).'),
                TextInput::make('days')
                    ->label('Aantal dagen Plus')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(365)
                    ->default(7),
                TextInput::make('max_uses')
                    ->label('Max. aantal keer te gebruiken')
                    ->numeric()
                    ->minValue(1)
                    ->helperText('Leeg laten = onbeperkt.'),
                TextInput::make('uses_count')
                    ->label('Al gebruikt')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit'),
                DateTimePicker::make('expires_at')
                    ->label('Geldig tot')
                    ->native(false)
                    ->seconds(false)
                    ->helperText('Leeg laten = geen einddatum.'),
                Toggle::make('is_active')
                    ->label('Actief')
                    ->default(true),
            ]);
    }
}
