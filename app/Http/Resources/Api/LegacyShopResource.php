<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Category;
use App\Models\Media;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/** @mixin Shop */
final class LegacyShopResource extends JsonResource
{
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
        return [
            'id' => $this->id,
            'societe' => $this->company,
            'slug' => $this->slug,
            'slugname' => $this->slug,
            'rue' => $this->street,
            'numero' => $this->number,
            'cp' => $this->postal_code,
            'localite' => $this->city,
            'telephone' => $this->phone,
            'telephone_autre' => $this->phone_other,
            'gsm' => $this->mobile,
            'email' => $this->email,
            'website' => $this->website,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'instagram' => $this->instagram,
            'tiktok' => $this->tiktok,
            'youtube' => $this->youtube,
            'linkedin' => $this->linkedin,
            'google_plus' => '',
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'centreville' => $this->hasTag('Centre ville'),
            'midi' => $this->hasTag('Ouvert le midi'),
            'pmr' => $this->hasTag('Pmr'),
            'click_collect' => $this->hasTag('Click & Collect'),
            'ecommerce' => $this->hasTag('Ecommerce'),
            'numero_tva' => $this->vat_number,
            'fonction' => $this->function,
            'civilite' => $this->civility,
            'nom' => $this->last_name,
            'prenom' => $this->first_name,
            'contact_rue' => $this->contact_street,
            'contact_num' => $this->contact_number,
            'contact_cp' => $this->contact_postal_code,
            'contact_localite' => $this->contact_city,
            'contact_telephone' => $this->contact_phone,
            'contact_telephone_autre' => $this->contact_phone_other,
            'contact_gsm' => $this->contact_mobile,
            'contact_email' => $this->contact_email,
            'comment1' => $this->comment1,
            'comment2' => $this->comment2,
            'comment3' => $this->comment3,
            'note' => $this->note,
            'newsletter' => '',
            'newsletter_date' => '',
            'classements' => $this->mapCategories(),
            'horaires' => $this->mapSchedules(),
            'images' => $this->mapImages(),
            'tags' => $this->mapTags(),
            'tagsObject' => $this->mapTagsObject(),
            'photos' => $this->mapPhotos(),
            'logo' => $this->mapLogo(),
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function mapCategories(): array
    {
        if (! $this->relationLoaded('categories')) {
            return [];
        }

        return $this->categories->map(function (Category $category): array {
            $ancestors = $this->getAncestors($category);
            $root = $ancestors->first();
            $lvl = $ancestors->count();
            $pathWithSelf = $ancestors->concat([$category]);

            return [
                'id' => $category->id,
                'name' => $category->name,
                'lvl' => $lvl,
                'lft' => '',
                'rgt' => '',
                'root' => $root ? (string) $root->id : (string) $category->id,
                'description' => $category->description,
                'logo' => $category->logo ?? '',
                'icon' => $category->icon,
                'slugname' => $category->slug,
                'slug' => $category->slug,
                'parent' => $category->parent_id,
                'path' => $pathWithSelf->map(fn (Category $ancestor): array => [
                    'id' => $ancestor->id,
                    'parent_id' => $ancestor->parent_id ?? 0,
                    'slugname' => $ancestor->slug,
                    'slug' => $ancestor->slug,
                    'name' => $ancestor->name,
                    'lvl' => 0,
                    'lft' => '',
                    'rgt' => '',
                    'root' => $root ? (string) $root->id : (string) $ancestor->id,
                    'mobile' => '',
                    'logo' => $ancestor->logo ? 'https://www.marche.be/logo/adl/categories/'.$ancestor->logo : 'https://www.marche.be/logo/adl/categories/',
                    'icon' => $ancestor->icon ? 'https://www.marche.be/logo/adl/categories/'.$ancestor->icon : 'https://www.marche.be/logo/adl/categories/',
                    'description' => $ancestor->description,
                    'logo_blanc' => $ancestor->logo_white,
                    'created_at' => $ancestor->created_at?->toIso8601String(),
                    'updated_at' => $ancestor->updated_at?->toIso8601String(),
                ])->values()->all(),
            ];
        })->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function mapSchedules(): array
    {
        if (! $this->relationLoaded('schedules')) {
            return [];
        }

        return $this->schedules->map(fn ($schedule): array => [
            'id' => $schedule->id,
            'day' => $schedule->day,
            'media_path' => $schedule->media_path,
            'is_open_at_lunch' => (int) $schedule->is_open_at_lunch,
            'is_rdv' => (int) $schedule->is_by_appointment,
            'morning_start' => $schedule->morning_start,
            'morning_end' => $schedule->morning_end,
            'noon_start' => $schedule->noon_start,
            'noon_end' => $schedule->noon_end,
            'fiche_id' => $schedule->shop_id,
            'is_closed' => (int) $schedule->is_closed,
        ])->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function mapImages(): array
    {
        if (! $this->relationLoaded('medias')) {
            return [];
        }

        return $this->medias->map(fn (Media $image): array => [
            'id' => $image->id,
            'fiche_id' => $image->shop_id,
            'principale' => $image->is_main,
            'image_name' => basename($image->file_name),
            'mime' => $image->mime_type,
            'updated_at' => $image->updated_at?->format('Y-m-d H:i:s'),
        ])->all();
    }

    /**
     * @return array<string, string>
     */
    private function mapTags(): array
    {
        if (! $this->relationLoaded('tags')) {
            return [];
        }

        $result = [];

        foreach ($this->tags as $tag) {
            $result[$tag->slug] = $tag->name;
        }

        return $result;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function mapTagsObject(): array
    {
        if (! $this->relationLoaded('tags')) {
            return [];
        }

        $result = [];

        foreach ($this->tags as $tag) {
            $result[$tag->slug] = [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
                'slugname' => $tag->slug,
                'color' => $tag->color,
                'icon' => $tag->icon,
            ];
        }

        return $result;
    }

    /**
     * @return array<int, string>
     */
    private function mapPhotos(): array
    {
        if (! $this->relationLoaded('medias')) {
            return [];
        }

        return $this->medias
            ->map(fn (Media $image): string => 'https://bottin.marche.be/'.$image->file_name)
            ->values()
            ->all();
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

    private function mapLogo(): ?string
    {
        if (! $this->relationLoaded('medias')) {
            return null;
        }

        $mainImage = $this->medias->first(fn (Media $image): bool => $image->is_main);

        if ($mainImage === null) {
            return $this->medias->isNotEmpty()
                ? 'https://bottin.marche.be/'.$this->medias->first()->file_name
                : null;
        }

        return 'https://bottin.marche.be/'.$mainImage->file_name;
    }
}
