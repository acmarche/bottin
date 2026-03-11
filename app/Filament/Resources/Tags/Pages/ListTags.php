<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tags\Pages;

use App\Filament\Resources\TagGroups\Pages\ManageTagGroups;
use App\Filament\Resources\Tags\TagResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

final class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajouter un tag')
                ->icon('tabler-plus'),
            Action::make('manageTagGroups')
                ->label('Gérer les groupes de tags')
                ->icon(Heroicon::OutlinedRectangleGroup)
                ->url(ManageTagGroups::getUrl()),
        ];
    }
}
