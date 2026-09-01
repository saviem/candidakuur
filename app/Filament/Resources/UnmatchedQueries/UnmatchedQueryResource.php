<?php

namespace App\Filament\Resources\UnmatchedQueries;

use App\Enums\ProductStatus;
use App\Filament\Resources\UnmatchedQueries\Pages\ManageUnmatchedQueries;
use App\Models\Allergy;
use App\Models\Category;
use App\Models\Product;
use App\Models\UnmatchedQuery;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class UnmatchedQueryResource extends Resource
{
    protected static ?string $model = UnmatchedQuery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static ?string $recordTitleAttribute = 'query';

    protected static ?string $navigationLabel = 'Gemiste zoekopdrachten';

    protected static ?string $modelLabel = 'gemiste zoekopdracht';

    protected static ?string $pluralModelLabel = 'Gemiste zoekopdrachten';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()->whereNull('converted_product_id')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('query')->label('Zoekterm')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('query')
            ->columns([
                TextColumn::make('query')->label('Zoekterm')->searchable()->sortable(),
                TextColumn::make('count')->label('Aantal')->sortable(),
                TextColumn::make('last_seen_at')->label('Laatst')->since()->sortable(),
                TextColumn::make('convertedProduct.name')->label('Omgezet naar')->placeholder('—'),
            ])
            ->defaultSort('count', 'desc')
            ->filters([
                TernaryFilter::make('converted_product_id')
                    ->label('Omgezet')
                    ->nullable()
                    ->trueLabel('Al omgezet')
                    ->falseLabel('Nog open')
                    ->placeholder('Alle'),
            ])
            ->recordActions([
                Action::make('makeProduct')
                    ->label('Maak product')
                    ->icon(Heroicon::Plus)
                    ->visible(fn (UnmatchedQuery $record) => $record->converted_product_id === null)
                    ->fillForm(fn (UnmatchedQuery $record) => [
                        'name' => Str::ucfirst($record->query),
                        'slug' => Str::slug($record->query),
                        'aliases' => [$record->query],
                    ])
                    ->schema([
                        Select::make('category_id')
                            ->label('Categorie')
                            ->options(fn () => Category::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options(collect(ProductStatus::cases())->mapWithKeys(
                                fn (ProductStatus $status) => [$status->value => $status->label()],
                            ))
                            ->required(),
                        TextInput::make('name')->label('Naam')->required(),
                        TextInput::make('slug')->required(),
                        Textarea::make('why')->label('Waarom')->required()->rows(4),
                        Textarea::make('conditions')->label('Voorwaarden')->rows(2),
                        TagsInput::make('aliases')->label('Zoekaliassen'),
                        CheckboxList::make('allergies')
                            ->label('Allergieën')
                            ->options(fn () => Allergy::query()->orderBy('sort_order')->pluck('name', 'id'))
                            ->columns(2),
                    ])
                    ->action(function (UnmatchedQuery $record, array $data) {
                        $product = Product::query()->create([
                            'category_id' => $data['category_id'],
                            'name' => $data['name'],
                            'slug' => $data['slug'],
                            'status' => $data['status'],
                            'why' => $data['why'],
                            'conditions' => $data['conditions'] ?? null,
                            'aliases' => $data['aliases'] ?? [],
                            'access' => 'public',
                        ]);
                        $product->allergies()->sync($data['allergies'] ?? []);
                        $record->update(['converted_product_id' => $product->id]);
                    })
                    ->successNotificationTitle('Product toegevoegd aan de kennisbank'),
                DeleteAction::make(),
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
            'index' => ManageUnmatchedQueries::route('/'),
        ];
    }
}
