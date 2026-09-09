<?php

declare(strict_types=1);

namespace App\Filament\Resources\Histories\Tables;

use App\Enums\HistoryActionEnum;
use App\Models\History;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class HistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->state(fn (History $record): HistoryActionEnum => $record->action()),
                TextColumn::make('shop.company')
                    ->label('Commerce')
                    ->state(fn (History $record): ?string => $record->shopName())
                    ->description(fn (History $record): ?string => $record->shop === null && $record->shopName() !== null ? 'Commerce supprimé' : null)
                    ->placeholder('—')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->whereHas('shop', fn (Builder $shopQuery): Builder => $shopQuery->where('company', 'like', "%{$search}%"))
                        ->orWhere(fn (Builder $lifecycleQuery): Builder => $lifecycleQuery
                            ->whereNull('shop_id')
                            ->where('property', History::LIFECYCLE_PROPERTY)
                            ->where(fn (Builder $valueQuery): Builder => $valueQuery
                                ->where('new_value', 'like', "%{$search}%")
                                ->orWhere('old_value', 'like', "%{$search}%"))))
                    ->sortable(),
                TextColumn::make('property')
                    ->label('Champ')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('new_value')
                    ->label('Changement')
                    ->state(fn (History $record): ?string => $record->new_value ?? $record->old_value)
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
            ->filters([
                SelectFilter::make('action')
                    ->label('Action')
                    ->options(HistoryActionEnum::class)
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        HistoryActionEnum::Created->value => $query
                            ->where('property', History::LIFECYCLE_PROPERTY)
                            ->whereNotNull('new_value'),
                        HistoryActionEnum::Deleted->value => $query
                            ->where('property', History::LIFECYCLE_PROPERTY)
                            ->whereNull('new_value'),
                        HistoryActionEnum::Updated->value => $query
                            ->where('property', '!=', History::LIFECYCLE_PROPERTY),
                        default => $query,
                    }),
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
