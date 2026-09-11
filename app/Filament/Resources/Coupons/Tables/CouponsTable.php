<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Code')->searchable()->sortable()->copyable(),
                TextColumn::make('label')->label('Label')->toggleable()->placeholder('—'),
                TextColumn::make('days')->label('Dagen')->sortable(),
                TextColumn::make('uses_count')
                    ->label('Gebruikt')
                    ->formatStateUsing(fn ($state, $record): string => $record->max_uses === null
                        ? "{$state}"
                        : "{$state} / {$record->max_uses}")
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Geldig tot')
                    ->dateTime('d-m-Y')
                    ->placeholder('—')
                    ->sortable(),
                IconColumn::make('is_active')->label('Actief')->boolean(),
                TextColumn::make('created_at')->label('Aangemaakt')->dateTime('d-m-Y')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_active')->label('Actief'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
