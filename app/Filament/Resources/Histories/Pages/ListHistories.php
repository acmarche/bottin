<?php

declare(strict_types=1);

namespace App\Filament\Resources\Histories\Pages;

use App\Filament\Resources\Histories\HistoryResource;
use Filament\Resources\Pages\ListRecords;

final class ListHistories extends ListRecords
{
    protected static string $resource = HistoryResource::class;

    public function getTitle(): string
    {
        return 'Historique des modifications';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
