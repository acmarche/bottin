<?php

declare(strict_types=1);

namespace App\Filament\Resources\TagGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

final class TagGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Toggle::make('private')
                    ->label('Privé')
                    ->helperText('Le public ne pourra pas voir ce groupe de tag.'),
            ]);
    }
}
