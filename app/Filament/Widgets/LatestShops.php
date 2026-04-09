<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\Shops\ShopResource;
use App\Models\Shop;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

final class LatestShops extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Shop::query()->latest())

            ->columns([
                TextColumn::make('company')
                    ->label('Entreprise')
                    ->url(fn (Shop $record): string => ShopResource::getUrl('view', ['record' => $record])),
                TextColumn::make('city')
                    ->label('Localité'),
                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(15)
            ->defaultSort('created_at', 'desc');
    }

    protected function getHeading(): ?string
    {
        return 'Les derniers inscrits';
    }
}
