<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointOfSales\Pages;

use App\Filament\Resources\PointOfSales\PointOfSaleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

final class ManagePointOfSales extends ManageRecords
{
    protected static string $resource = PointOfSaleResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getAllTableRecordsCount().' points de vente';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajouter un point de vente')
                ->icon('tabler-plus'),
        ];
    }
}
