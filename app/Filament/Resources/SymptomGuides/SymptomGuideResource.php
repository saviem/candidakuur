<?php

namespace App\Filament\Resources\SymptomGuides;

use App\Filament\Resources\SymptomGuides\Pages\CreateSymptomGuide;
use App\Filament\Resources\SymptomGuides\Pages\EditSymptomGuide;
use App\Filament\Resources\SymptomGuides\Pages\ListSymptomGuides;
use App\Models\SymptomGuide;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TagsInput;
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

class SymptomGuideResource extends Resource
{
    protected static ?string $model = SymptomGuide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Wat nu?';

    protected static ?string $modelLabel = 'symptoomkaart';

    protected static ?string $pluralModelLabel = 'Wat nu?';

    protected static ?int $navigationSort = 7;

    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Titel')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TagsInput::make('tags')
                    ->label('Tags')
                    ->placeholder('Tag toevoegen'),
                Textarea::make('summary')
                    ->label('Samenvatting / teaser')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('body_common')
                    ->label('Komt vaker voor')
                    ->rows(5)
                    ->columnSpanFull(),
                Textarea::make('body_practical')
                    ->label('Praktische hulp')
                    ->rows(5)
                    ->columnSpanFull(),
                Textarea::make('body_contact')
                    ->label('Wanneer contact opnemen')
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Volgorde')
                    ->numeric()
                    ->default(0),
                Toggle::make('published')
                    ->label('Gepubliceerd')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')->label('Titel')->searchable()->sortable(),
                TextColumn::make('summary')->label('Samenvatting')->limit(50)->toggleable(),
                IconColumn::make('published')->label('Live')->boolean(),
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
            'index' => ListSymptomGuides::route('/'),
            'create' => CreateSymptomGuide::route('/create'),
            'edit' => EditSymptomGuide::route('/{record}/edit'),
        ];
    }
}
