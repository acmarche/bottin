<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Pages;

use App\Filament\Resources\Shops\ShopResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

final class ListShops extends ListRecords
{
    protected static string $resource = ShopResource::class;

    protected static ?string $navigationLabel = 'Fiches';

    public function getTitle(): string|Htmlable
    {
        return $this->getAllTableRecordsCount().' fiches';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajouter une fiche')
                ->icon('tabler-plus'),
        ];
    }
}
