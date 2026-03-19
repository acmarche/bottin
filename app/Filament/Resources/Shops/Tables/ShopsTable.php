<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Tables;

use App\Filament\Resources\Shops\ShopResource;
use App\Models\Locality;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class ShopsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company')
                    ->label('Société')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('street')
                    ->label('Rue')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('city')
                    ->label('Ville')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('mobile')
                    ->label('Mobile')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('categories_count')
                    ->label('Catégories')
                    ->counts('categories')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tags_count')
                    ->label('Tags')
                    ->counts('tags')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('pointOfSale')
                    ->label('Point de vente')
                    ->relationship('pointOfSale', 'name')
                    ->preload(),
                TernaryFilter::make('enabled')
                    ->label('Actif'),
                TernaryFilter::make('without_category')
                    ->label('Sans catégorie')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereDoesntHave('categories'),
                        false: fn (Builder $query): Builder => $query->whereHas('categories'),
                    ),
                SelectFilter::make('tags')
                    ->label('Tag')
                    ->relationship('tags', 'name')
                    ->preload()
                    ->multiple(),
                SelectFilter::make('city')
                    ->label('Localité')
                    ->options(fn (): array => Locality::query()->orderBy('name')->pluck('name', 'name')->all())
                    ->searchable(),
            ])
            ->defaultSort('company', 'asc')
            ->filtersFormColumns(3)
            ->recordAction(ViewAction::class)
            ->defaultPaginationPageOption(50)
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

    public static function inline(Table $table): Table
    {
        return $table->recordTitleAttribute('company')
            ->columns([
                TextColumn::make('company')
                    ->label('Société')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('city')
                    ->label('Ville')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable(),
            ])
            ->recordAction(ViewAction::class)
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record): string => ShopResource::getUrl('view', ['record' => $record])),
            ]);

    }
}
