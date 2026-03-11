<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\RelationManagers;

use App\Filament\Resources\Shops\Schemas\MediaForm;
use App\Filament\Resources\Shops\Tables\MediaTables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

final class MediasRelationManager extends RelationManager
{
    protected static string $relationship = 'medias';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return MediaForm::configure($schema, $this->getOwnerRecord());
    }

    public function table(Table $table): Table
    {
        return MediaTables::configure($table);
    }
}
