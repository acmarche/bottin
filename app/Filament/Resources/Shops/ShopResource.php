<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops;

use App\Filament\Resources\Shops\Pages\CreateShop;
use App\Filament\Resources\Shops\Pages\EditShop;
use App\Filament\Resources\Shops\Pages\ListShops;
use App\Filament\Resources\Shops\Pages\ViewShop;
use App\Filament\Resources\Shops\RelationManagers\CategoriesRelationManager;
use App\Filament\Resources\Shops\RelationManagers\HistoriesRelationManager;
use App\Filament\Resources\Shops\RelationManagers\MediasRelationManager;
use App\Filament\Resources\Shops\RelationManagers\SchedulesRelationManager;
use App\Filament\Resources\Shops\Schemas\ShopForm;
use App\Filament\Resources\Shops\Tables\ShopsTable;
use App\Models\Shop;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class ShopResource extends Resource
{
    protected static ?string $model = Shop::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|UnitEnum|null $navigationGroup = 'Shops';

    protected static ?string $navigationLabel = 'Fiches';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'company';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'company',
            'email',
            'phone',
            'city',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ShopForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShopsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CategoriesRelationManager::class,
            MediasRelationManager::class,
            SchedulesRelationManager::class,
            HistoriesRelationManager::class,
            //   SituationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShops::route('/'),
            'create' => CreateShop::route('/create'),
            'view' => ViewShop::route('/{record}'),
            'edit' => EditShop::route('/{record}/edit'),
        ];
    }
}
