<?php

declare(strict_types=1);

namespace App\Filament\Resources\Localities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class LocalityForm
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
