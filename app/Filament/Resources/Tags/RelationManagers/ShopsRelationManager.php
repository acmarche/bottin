<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tags\RelationManagers;

use App\Filament\Resources\Shops\Tables\ShopsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

final class ShopsRelationManager extends RelationManager
{
    protected static string $relationship = 'shops';

    protected static ?string $title = 'Fiches';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return ShopsTable::inline($table);
    }
}
