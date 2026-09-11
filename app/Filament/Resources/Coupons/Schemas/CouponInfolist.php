<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CouponInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code')->label('Code')->copyable(),
                TextEntry::make('label')->label('Label')->placeholder('—'),
                TextEntry::make('days')->label('Dagen'),
                TextEntry::make('uses_count')
                    ->label('Gebruikt')
                    ->formatStateUsing(function ($state, $record): string {
                        $max = $record->max_uses;

                        return $max === null ? "{$state} / ∞" : "{$state} / {$max}";
                    }),
                TextEntry::make('expires_at')
                    ->label('Geldig tot')
                    ->dateTime('d-m-Y H:i')
                    ->placeholder('Geen einddatum'),
                IconEntry::make('is_active')->label('Actief')->boolean(),
                TextEntry::make('created_at')->label('Aangemaakt')->dateTime('d-m-Y H:i'),
            ]);
    }
}
