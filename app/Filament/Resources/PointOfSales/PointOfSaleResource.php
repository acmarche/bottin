<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointOfSales;

use App\Filament\Resources\PointOfSales\Pages\ManagePointOfSales;
use App\Filament\Resources\PointOfSales\Schemas\PointOfSaleForm;
use App\Filament\Resources\PointOfSales\Tables\PointOfSalesTable;
use App\Models\PointOfSale;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class PointOfSaleResource extends Resource
{
    protected static ?string $model = PointOfSale::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Points de vente';

    public static function form(Schema $schema): Schema
    {
        return PointOfSaleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PointOfSalesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePointOfSales::route('/'),
        ];
    }
}
