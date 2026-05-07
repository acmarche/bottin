<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Shop;
use App\Models\Tag;
use Illuminate\Support\Collection;

final class TagShopsExport
{
    /** @param  array<int, string>  $selectedColumns */
    public function __construct(
        private Tag $tag,
        private array $selectedColumns,
    ) {}

    /** @return Collection<int, Shop> */
    public function collection(): Collection
    {
        return $this->tag->shops()->get();
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        $allColumns = collect(CategoryShopsExport::availableColumns())->flatMap(
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
            ->map(fn (string $column): mixed => $shop->{$column})
            ->all();
    }
}
