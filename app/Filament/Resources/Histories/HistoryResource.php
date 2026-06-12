<?php

declare(strict_types=1);

namespace App\Filament\Resources\Histories;

use App\Filament\Resources\Histories\Pages\ListHistories;
use App\Filament\Resources\Histories\Pages\ViewHistory;
use App\Filament\Resources\Histories\Schemas\HistoryInfolist;
use App\Filament\Resources\Histories\Tables\HistoriesTable;
use App\Models\History;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class HistoryResource extends Resource
{
    protected static ?string $model = History::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Shops';

    protected static ?string $navigationLabel = 'Historique';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'property';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'property',
            'made_by',
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return HistoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HistoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHistories::route('/'),
            'view' => ViewHistory::route('/{record}'),
        ];
    }
}
