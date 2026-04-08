<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\RelationManagers;

use App\Filament\Resources\Shops\Schemas\MediaForm;
use App\Filament\Resources\Shops\Tables\MediaTables;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

final class MediasRelationManager extends RelationManager
{
    protected static string $relationship = 'media';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return MediaForm::configureEdit($schema);
    }

    public function table(Table $table): Table
    {
        return MediaTables::configure($table)
            ->headerActions([
                MediaForm::createAction(CreateAction::make()),
            ])
            ->recordActions([
                MediaForm::editAction(EditAction::make()),
                DeleteAction::make(),
            ]);
    }
}
