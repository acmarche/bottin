<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Category;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class SelectedCategoriesShopsExport
{
    public const string CATEGORIES_COLUMN = 'selected_categories';

    /**
     * Only the given categories are matched: descendants must be listed explicitly,
     * so that a category whose children were partially selected keeps that selection.
     *
     * @param  array<int, int>  $categoryIds
     * @param  array<int, string>  $selectedColumns
     */
    public function __construct(
        private array $categoryIds,
        private array $selectedColumns = [],
    ) {}

    /**
     * @return array<string, array<string, string>>
     */
    public static function availableColumns(): array
    {
        $columns = CategoryShopsExport::availableColumns();

        return [
            'Business' => $columns['Business'],
            'Catégories' => [self::CATEGORIES_COLUMN => 'Catégories'],
        ] + $columns;
    }

    /** @return Collection<int, Shop> */
    public function collection(): Collection
    {
        return $this->query()
            ->with('categories.parent.parent.parent.parent')
            ->orderBy('company')
            ->get();
    }

    public function count(): int
    {
        return $this->query()->count();
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        $allColumns = collect(self::availableColumns())->flatMap(
            fn (array $columns): array => $columns,
        );

        return collect($this->selectedColumns)
            ->map(fn (string $column): string => $allColumns->get($column, $column))
            ->all();
    }

    /** @return array<int, mixed> */
    public function map(Shop $shop): array
    {
        return collect($this->selectedColumns)
            ->map(fn (string $column): mixed => $column === self::CATEGORIES_COLUMN
                ? $this->selectedCategoryPaths($shop)
                : $shop->{$column})
            ->all();
    }

    /**
     * Lists only the selected categories of the shop, which explain why it is exported.
     */
    private function selectedCategoryPaths(Shop $shop): string
    {
        return $shop->categories
            ->whereIn('id', $this->categoryIds)
            ->map(fn (Category $category): string => $category->fullPath())
            ->sort()
            ->implode('; ');
    }

    /** @return Builder<Shop> */
    private function query(): Builder
    {
        return Shop::query()->whereHas('categories', function (Builder $query): void {
            $query->whereIn('categories.id', $this->categoryIds);
        });
    }
}
