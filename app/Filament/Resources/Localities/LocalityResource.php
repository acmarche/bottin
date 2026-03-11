<?php

declare(strict_types=1);

namespace App\Filament\Resources\Localities;

use App\Filament\Resources\Localities\Pages\ManageLocalities;
use App\Filament\Resources\Localities\Schemas\LocalityForm;
use App\Filament\Resources\Localities\Tables\LocalitiesTable;
use App\Models\Locality;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class LocalityResource extends Resource
{
    protected static ?string $model = Locality::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeEuropeAfrica;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 11;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Les localités';

    public static function form(Schema $schema): Schema
    {
        return LocalityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LocalitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLocalities::route('/'),
        ];
    }
}
