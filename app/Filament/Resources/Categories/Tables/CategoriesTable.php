<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->formatStateUsing(fn ($record): string => $record->parent_id !== null ? $record->fullPath() : $record->name)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('shops_count')
                    ->label('Commerces')
                    ->counts('shops')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('without_shop')
                    ->label('Sans fiche')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereDoesntHave('children')->whereDoesntHave('shops'),
                        false: fn (Builder $query): Builder => $query->whereDoesntHave('children')->whereHas('shops'),
                        blank: fn (Builder $query): Builder => $query->whereNull('parent_id'),
                    ),
            ])
            ->defaultPaginationPageOption(50)
            ->recordAction(ViewAction::class)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
