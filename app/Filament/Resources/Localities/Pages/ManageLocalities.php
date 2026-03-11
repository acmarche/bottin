<?php

declare(strict_types=1);

namespace App\Filament\Resources\Localities\Pages;

use App\Filament\Resources\Localities\LocalityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

final class ManageLocalities extends ManageRecords
{
    protected static string $resource = LocalityResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getAllTableRecordsCount().' localités';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajouter une localité')
                ->icon('tabler-plus'),
        ];
    }
}
