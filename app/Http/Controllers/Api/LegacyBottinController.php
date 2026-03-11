<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\LegacyCategoryResource;
use App\Http\Resources\Api\LegacyShopResource;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class LegacyBottinController
{
    public function commerces(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        return LegacyCategoryResource::collection($categories);
    }

    public function fiches(): AnonymousResourceCollection
    {
        $shops = Shop::query()
            ->where('enabled', true)
            ->with(['categories', 'schedules', 'medias', 'tags'])
            ->get();

        return LegacyShopResource::collection($shops);
    }

    public function fichesByCategory(Category $category): AnonymousResourceCollection
    {
        $shops = $category->shops()
            ->where('enabled', true)
            ->with(['categories', 'schedules', 'medias', 'tags'])
            ->get();

        return LegacyShopResource::collection($shops);
    }

    public function ficheById(Shop $shop): LegacyShopResource
    {
        $shop->load(['categories', 'schedules', 'medias', 'tags']);

        return new LegacyShopResource($shop);
    }

    public function ficheBySlug(string $slug): LegacyShopResource
    {
        $shop = Shop::query()
            ->where('slug', $slug)
            ->with(['categories', 'schedules', 'medias', 'tags'])
            ->firstOrFail();

        return new LegacyShopResource($shop);
    }
}
