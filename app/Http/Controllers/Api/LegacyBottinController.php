<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\LegacyCategoryResource;
use App\Http\Resources\Api\LegacyShopResource;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Http\JsonResponse;

final class LegacyBottinController
{
    public function commerces(): JsonResponse
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with('children.children')
            ->get();

        LegacyCategoryResource::preloadCategories();

        return response()->json(LegacyCategoryResource::collection($categories)->resolve());
    }

    public function fiches(): JsonResponse
    {
        $shops = Shop::query()
            ->where('enabled', true)
            ->with(['categories', 'schedules', 'medias', 'tags'])
            ->get();

        LegacyShopResource::preloadCategories();

        return response()->json(LegacyShopResource::collection($shops)->resolve());
    }

    public function fichesByCategory(Category $category): JsonResponse
    {
        $shops = $category->shops()
            ->where('enabled', true)
            ->with(['categories', 'schedules', 'medias', 'tags'])
            ->get();

        LegacyShopResource::preloadCategories();

        return response()->json(LegacyShopResource::collection($shops)->resolve());
    }

    public function ficheById(Shop $shop): JsonResponse
    {
        $shop->load(['categories', 'schedules', 'medias', 'tags']);

        LegacyShopResource::preloadCategories();

        return response()->json((new LegacyShopResource($shop))->resolve());
    }

    public function ficheBySlug(string $slug): JsonResponse
    {
        $shop = Shop::query()
            ->where('slug', $slug)
            ->with(['categories', 'schedules', 'medias', 'tags'])
            ->firstOrFail();

        LegacyShopResource::preloadCategories();

        return response()->json((new LegacyShopResource($shop))->resolve());
    }
}
