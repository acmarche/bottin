<?php

declare(strict_types=1);

namespace App\Filament\Resources\TagGroups;

use App\Filament\Resources\TagGroups\Pages\ManageTagGroups;
use App\Filament\Resources\TagGroups\Schemas\TagGroupForm;
use App\Filament\Resources\TagGroups\Tables\TagGroupsTable;
use App\Models\TagGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

final class TagGroupResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = TagGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'Groupes de tags';

    public static function form(Schema $schema): Schema
    {
        return TagGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagGroupsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTagGroups::route('/'),
        ];
    }
}
