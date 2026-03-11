<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Collection;

final class ShopRepository
{
    public static function searchByName(string $query): Collection
    {
        if (mb_strlen($query) < 2) {
            return new Collection();
        }

        return Shop::query()
            ->where('company', 'like', "%{$query}%")
            ->orderBy('company')
            ->limit(10)
            ->get(['id', 'company', 'city']);
    }
}
