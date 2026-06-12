<?php

declare(strict_types=1);

namespace App\Filament\Resources\Histories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class HistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shop.company')
                    ->label('Commerce')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('property')
                    ->label('Champ')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('new_value')
                    ->label('Changement')
                    ->html()
                    ->limit(120)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (mb_strlen((string) $state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),
                TextColumn::make('made_by')
                    ->label('Ajouté par')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Modifié le')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(50)
            ->recordAction(ViewAction::class)
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
