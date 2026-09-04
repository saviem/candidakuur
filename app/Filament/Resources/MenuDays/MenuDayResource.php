<?php

namespace App\Filament\Resources\MenuDays;

use App\Filament\Resources\MenuDays\Pages\CreateMenuDay;
use App\Filament\Resources\MenuDays\Pages\EditMenuDay;
use App\Filament\Resources\MenuDays\Pages\ListMenuDays;
use App\Models\MenuDay;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MenuDayResource extends Resource
{
    protected static ?string $model = MenuDay::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $recordTitleAttribute = 'weekday';

    protected static ?string $navigationLabel = 'Menu';

    protected static ?string $modelLabel = 'menudag';

    protected static ?string $pluralModelLabel = 'Menu';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('day')
                    ->label('Dagnummer')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->unique(ignoreRecord: true),
                Select::make('weekday')
                    ->label('Weekdag')
                    ->options([
                        'Maandag' => 'Maandag',
                        'Dinsdag' => 'Dinsdag',
                        'Woensdag' => 'Woensdag',
                        'Donderdag' => 'Donderdag',
                        'Vrijdag' => 'Vrijdag',
                        'Zaterdag' => 'Zaterdag',
                        'Zondag' => 'Zondag',
                    ])
                    ->required(),
                Select::make('week')
                    ->label('Week')
                    ->options([
                        1 => 'Week 1',
                        2 => 'Week 2',
                        3 => 'Week 3',
                    ])
                    ->required(),
                Repeater::make('meals')
                    ->label('Maaltijden')
                    ->relationship()
                    ->schema([
                        TextInput::make('label')
                            ->label('Moment')
                            ->placeholder('Ontbijt, lunch, diner…')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('text')
                            ->label('Wat eten')
                            ->required()
                            ->rows(3),
                        Textarea::make('note')
                            ->label('Notitie')
                            ->rows(2),
                        Select::make('products')
                            ->label('Producten')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                    ])
                    ->orderColumn('sort_order')
                    ->collapsible()
                    ->itemLabel(fn (array $state): string => filled($state['label'] ?? null) ? $state['label'] : 'Maaltijd')
                    ->addActionLabel('Maaltijd toevoegen')
                    ->defaultItems(1)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('weekday')
            ->columns([
                TextColumn::make('day')->label('Dag')->sortable(),
                TextColumn::make('weekday')->label('Weekdag')->searchable(),
                TextColumn::make('week')->label('Week')->sortable(),
                TextColumn::make('meals_count')->counts('meals')->label('Maaltijden'),
            ])
            ->defaultSort('day')
            ->filters([
                SelectFilter::make('week')
                    ->options([
                        1 => 'Week 1',
                        2 => 'Week 2',
                        3 => 'Week 3',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenuDays::route('/'),
            'create' => CreateMenuDay::route('/create'),
            'edit' => EditMenuDay::route('/{record}/edit'),
        ];
    }
}
