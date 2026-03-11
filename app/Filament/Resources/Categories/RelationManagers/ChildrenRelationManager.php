<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories\RelationManagers;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    protected static ?string $title = 'Sous-catégories';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('children_count')
                    ->label('Sous-catégories')
                    ->counts('children'),
                TextColumn::make('shops_count')
                    ->label('Commerces')
                    ->counts('shops'),
            ])
            ->recordAction(ViewAction::class)
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record): string => CategoryResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
