<?php

declare(strict_types=1);

namespace App\Filament\Resources\Histories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class HistoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('shop.company')
                    ->label('Commerce'),
                TextEntry::make('property')
                    ->label('Champ'),
                TextEntry::make('old_value')
                    ->label('Ancienne valeur')
                    ->html()
                    ->placeholder('—'),
                TextEntry::make('new_value')
                    ->label('Nouvelle valeur')
                    ->html()
                    ->placeholder('—'),
                TextEntry::make('made_by')
                    ->label('Ajouté par'),
                TextEntry::make('created_at')
                    ->label('Modifié le')
                    ->dateTime(),
            ]);
    }
}
