<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Flex::make([
                    Section::make('Détails')
                        ->icon(Heroicon::InformationCircle)
                        ->schema([
                            TextEntry::make('name')
                                ->label('Nom'),
                            TextEntry::make('description')
                                ->label('Description')
                                ->placeholder('—'),
                        ]),
                    Section::make('Infos')
                        ->icon(Heroicon::Cog6Tooth)
                        ->grow(false)
                        ->schema([
                            ColorEntry::make('color')
                                ->label('Couleur'),
                            TextEntry::make('icon')
                                ->label('Icône')
                                ->placeholder('—'),
                            TextEntry::make('slug')
                                ->label('Slug'),
                        ]),
                ])->from('md')
                    ->columnSpanFull(),
            ]);
    }
}
