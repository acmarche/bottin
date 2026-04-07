<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Category;
use App\Models\Shop;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * @implements WithMapping<Shop>
 */
final class CategoryShopsExport implements FromCollection, WithHeadings, WithMapping
{
    /** @param  array<int, string>  $selectedColumns */
    public function __construct(
        private Category $category,
        private array $selectedColumns,
    ) {}

    /**
     * @param  array<int, string>  $selectedColumns
     * @return array<string, array<string, string>>
     */
    public static function availableColumns(): array
    {
        return [
            'Business' => [
                'company' => 'Société',
                'vat_number' => 'Numéro TVA',
            ],
            'Address' => [
                'street' => 'Rue',
                'number' => 'Numéro',
                'postal_code' => 'Code postal',
                'city' => 'Ville',
            ],
            'Contact Phones' => [
                'phone' => 'Téléphone',
                'phone_other' => 'Autre téléphone',
                'mobile' => 'GSM',
            ],
            'Online' => [
                'email' => 'Email',
                'website' => 'Site web',
                'facebook' => 'Facebook',
                'instagram' => 'Instagram',
                'tiktok' => 'TikTok',
                'youtube' => 'YouTube',
                'linkedin' => 'LinkedIn',
                'twitter' => 'Twitter',
            ],
            'Geo' => [
                'longitude' => 'Longitude',
                'latitude' => 'Latitude',
            ],
            'Contact Person' => [
                'civility' => 'Civilité contact',
                'first_name' => 'Prénom contact',
                'last_name' => 'Nom contact',
                'function' => 'Fonction contact',
                'contact_phone' => 'Tél. contact',
                'contact_email' => 'Email contact',
            ],
            'Admin Contact' => [
                'admin_civility' => 'Civilité admin',
                'admin_first_name' => 'Prénom admin',
                'admin_last_name' => 'Nom admin',
                'admin_function' => 'Fonction admin',
                'admin_phone' => 'Tél. admin',
                'admin_email' => 'Email admin',
            ],
            'Notes' => [
                'comment1' => 'Commentaire 1',
                'comment2' => 'Commentaire 2',
                'comment3' => 'Commentaire 3',
                'note' => 'Note',
            ],
        ];
    }

    /** @return Collection<int, Shop> */
    public function collection(): Collection
    {
        $categoryIds = $this->category->descendantsAndSelfIds();

        return Shop::query()
            ->whereHas('categories', function ($query) use ($categoryIds): void {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->get();
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
    public function map($row): array
    {
        return collect($this->selectedColumns)
            ->map(fn (string $column): mixed => $row->{$column})
            ->all();
    }
}
