<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointOfSales\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class PointOfSaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
