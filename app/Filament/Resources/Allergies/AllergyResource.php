<?php

namespace App\Filament\Resources\Allergies;

use App\Filament\Resources\Allergies\Pages\CreateAllergy;
use App\Filament\Resources\Allergies\Pages\EditAllergy;
use App\Filament\Resources\Allergies\Pages\ListAllergies;
use App\Models\Allergy;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AllergyResource extends Resource
{
    protected static ?string $model = Allergy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Allergieën';

    protected static ?string $modelLabel = 'allergie';

    protected static ?string $pluralModelLabel = 'Allergieën';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Naam')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('short')
                    ->label('Korte regel')
                    ->maxLength(255),
                Toggle::make('is_main')
                    ->label('Hoofdallergeen')
                    ->default(false),
                Textarea::make('intro')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Volgorde')
                    ->numeric()
                    ->default(0),
                Select::make('products')
                    ->label('Producten')
                    ->relationship('products', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Naam')->searchable()->sortable(),
                TextColumn::make('short')->label('Kort')->toggleable(),
                IconColumn::make('is_main')->label('Hoofd')->boolean(),
                TextColumn::make('products_count')->counts('products')->label('Producten'),
                TextColumn::make('sort_order')->label('Volgorde')->sortable(),
            ])
            ->defaultSort('sort_order')
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
            'index' => ListAllergies::route('/'),
            'create' => CreateAllergy::route('/create'),
            'edit' => EditAllergy::route('/{record}/edit'),
        ];
    }
}
