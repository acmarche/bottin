<?php

declare(strict_types=1);

namespace App\Filament\Resources\TagGroups\Pages;

use App\Filament\Resources\TagGroups\TagGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

final class ManageTagGroups extends ManageRecords
{
    protected static string $resource = TagGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajouter un groupe de tags')
                ->icon('tabler-plus'),
        ];
    }
}
