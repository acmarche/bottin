<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Pages;

use App\Concerns\TracksHistoryTrait;
use App\Filament\Resources\Shops\ShopResource;
use App\Models\Category;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

final class EditShop extends EditRecord
{
    use TracksHistoryTrait;

    protected static string $resource = ShopResource::class;

    /**
     * @var array<string, array<int>>
     */
    private array $oldRelationshipIds = [];

    /**
     * Hide relation managers on Edit page - they are shown on View page only.
     */
    protected function getAllRelationManagers(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon('tabler-eye'),
        ];
    }

    protected function beforeSave(): void
    {
        $record = $this->getRecord();

        $this->oldRelationshipIds = [
            'categories' => $record->categories()->pluck('categories.id')->toArray(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        $relationships = [
            'categories' => [
                'old' => $this->oldRelationshipIds['categories'],
                'new' => $record->categories()->pluck('categories.id')->toArray(),
                'label' => 'classement',
                'getDisplayName' => fn (int $id): string => $this->getCategoryName($id),
            ],
        ];

        $this->trackRelationships($record, $relationships);
    }

    private function getCategoryName(int $id): string
    {
        $category = Category::find($id);

        return $category ? "{$category->name}" : "ID: {$id}";
    }
}
