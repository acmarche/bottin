<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/** @mixin Category */
final class LegacyCategoryResource extends JsonResource
{
    private const string LOGO_BASE_URL = 'https://www.marche.be/logo/adl/categories/';

    /** @var Collection<int, Category>|null */
    private static ?Collection $allCategories = null;

    public static function preloadCategories(): void
    {
        self::$allCategories = Category::all()->keyBy('id');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ancestors = $this->getAncestors($this->resource);
        $root = $ancestors->first();
        $lvl = $ancestors->count();

        return [
            'id' => $this->id,
            'parent' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'slugname' => $this->slug,
            'description' => $this->description,
            'mobile' => $this->mobile,
            'logo' => self::LOGO_BASE_URL.($this->logo ?? ''),
            'logo_blanc' => self::LOGO_BASE_URL.($this->logo_white ?? ''),
            'color' => $this->color,
            'icon' => self::LOGO_BASE_URL.($this->icon ?? ''),
            'lvl' => $lvl,
            'lft' => '',
            'rgt' => '',
            'root' => $root ? (string) $root->id : (string) $this->id,
            'path' => $this->mapPath($ancestors, $root),
            'enfants' => self::collection($this->whenLoaded('children')),
        ];
    }

    /**
     * @param  Collection<int, Category>  $ancestors
     * @return array<int, array<string, mixed>>
     */
    private function mapPath(Collection $ancestors, ?Category $root): array
    {
        $pathWithSelf = $ancestors->concat([$this->resource]);

        return $pathWithSelf->map(fn (Category $category): array => [
            'id' => $category->id,
            'parent_id' => $category->parent_id ?? 0,
            'slugname' => $category->slug,
            'slug' => $category->slug,
            'name' => $category->name,
            'lvl' => 0,
            'lft' => '',
            'rgt' => '',
            'root' => $root ? (string) $root->id : (string) $category->id,
            'mobile' => '',
            'logo' => self::LOGO_BASE_URL.($category->logo ?? ''),
            'icon' => self::LOGO_BASE_URL.($category->icon ?? ''),
            'description' => $category->description,
            'logo_blanc' => self::LOGO_BASE_URL.($category->logo_white ?? ''),
            'created_at' => $category->created_at?->toIso8601String(),
            'updated_at' => $category->updated_at?->toIso8601String(),
        ])->values()->all();
    }

    /**
     * @return Collection<int, Category>
     */
    private function getAncestors(Category $category): Collection
    {
        $ancestors = collect();
        $current = $category;
        $categories = self::$allCategories ?? collect();

        while ($current->parent_id !== null && $current->parent_id !== 0) {
            $current = $categories->get($current->parent_id);

            if ($current === null) {
                break;
            }

            $ancestors->prepend($current);
        }

        return $ancestors;
    }
}
