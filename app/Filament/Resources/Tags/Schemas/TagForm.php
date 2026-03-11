<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

final class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Select::make('tag_group_id')
                    ->label('Groupe')
                    ->relationship('tagGroup', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->unique('tag_groups', 'name')
                            ->maxLength(255),
                    ]),
                ColorPicker::make('color')
                    ->label('Couleur'),
                TextInput::make('icon')
                    ->label('Icône')
                    ->maxLength(255),
                Toggle::make('private')
                    ->label('Privé')
                    ->helperText('Le public ne pourra pas voir ce tag.'),
                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
