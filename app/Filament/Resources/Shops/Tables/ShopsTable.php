<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Tables;

use App\Filament\Resources\Shops\ShopResource;
use App\Models\Locality;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

final class ShopsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company')
                    ->label('Société')
                    ->limit(70)
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
                Filter::make('created_at')
                    ->label('Date de création')
                    ->columnSpanFull()
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Créé à partir du'),
                        DatePicker::make('created_until')
                            ->label("Créé jusqu'au"),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            $data['created_from'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date),
                        ))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Créé à partir du '.Carbon::parse($data['created_from'])->translatedFormat('d/m/Y'))
                                ->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make("Créé jusqu'au ".Carbon::parse($data['created_until'])->translatedFormat('d/m/Y'))
                                ->removeField('created_until');
                        }

                        return $indicators;
                    }),
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
