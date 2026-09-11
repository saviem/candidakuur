<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Naam')->searchable()->sortable(),
                TextColumn::make('email')->label('E-mail')->searchable()->sortable()->copyable(),
                TextColumn::make('plus_until')
                    ->label('Plus tot')
                    ->dateTime('d-m-Y H:i')
                    ->placeholder('Geen Plus')
                    ->sortable()
                    ->color(fn ($state): ?string => $state && $state->isFuture() ? 'success' : null),
                IconColumn::make('is_admin')->label('Admin')->boolean(),
                TextColumn::make('created_at')->label('Lid sinds')->dateTime('d-m-Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_admin')->label('Admin'),
                TernaryFilter::make('has_plus')
                    ->label('Plus')
                    ->queries(
                        true: fn (Builder $query) => $query->where('plus_until', '>', now()),
                        false: fn (Builder $query) => $query->where(function (Builder $q): void {
                            $q->whereNull('plus_until')->orWhere('plus_until', '<=', now());
                        }),
                    ),
            ])
            ->recordActions([
                Action::make('managePlus')
                    ->label('Plus')
                    ->icon('heroicon-o-sparkles')
                    ->fillForm(fn (User $record): array => [
                        'mode' => $record->isPlus() ? 'grant' : 'grant',
                        'days' => (int) config('services.mollie.plus_days', 30),
                    ])
                    ->form([
                        Select::make('mode')
                            ->label('Actie')
                            ->options([
                                'grant' => 'Dagen geven / verlengen',
                                'revoke' => 'Plus intrekken',
                            ])
                            ->required()
                            ->live(),
                        TextInput::make('days')
                            ->label('Aantal dagen')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(3650)
                            ->default((int) config('services.mollie.plus_days', 30))
                            ->visible(fn (Get $get): bool => $get('mode') === 'grant'),
                    ])
                    ->action(function (User $record, array $data): void {
                        if (($data['mode'] ?? '') === 'revoke') {
                            $record->forceFill(['plus_until' => null])->save();

                            Notification::make()
                                ->title('Plus ingetrokken')
                                ->body("{$record->name} heeft geen Plus meer.")
                                ->success()
                                ->send();

                            return;
                        }

                        $days = (int) ($data['days'] ?? 0);
                        $base = $record->plus_until !== null && $record->plus_until->isFuture()
                            ? $record->plus_until->copy()
                            : now();

                        $record->forceFill([
                            'plus_until' => $base->addDays($days),
                        ])->save();

                        Notification::make()
                            ->title('Plus geactiveerd')
                            ->body("{$record->name} heeft Plus tot ".$record->plus_until->format('d-m-Y H:i').'.')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, User $record): void {
                        if (auth()->id() === $record->id) {
                            Notification::make()
                                ->title('Je kunt jezelf niet verwijderen')
                                ->danger()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, $records): void {
                            if ($records->contains(fn (User $user): bool => $user->id === auth()->id())) {
                                Notification::make()
                                    ->title('Je kunt jezelf niet verwijderen')
                                    ->danger()
                                    ->send();
                                $action->cancel();
                            }
                        }),
                ]),
            ]);
    }
}
