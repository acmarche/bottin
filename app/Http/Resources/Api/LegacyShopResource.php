<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Category;
use App\Models\Media;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Shop */
final class LegacyShopResource extends JsonResource
{
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
            'fax' => $this->fax,
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
            'centreville' => $this->city_center,
            'midi' => $this->open_at_lunch,
            'pmr' => $this->pmr,
            'click_collect' => $this->click_collect,
            'ecommerce' => $this->ecommerce,
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
            'contact_fax' => $this->contact_fax,
            'contact_gsm' => $this->contact_mobile,
            'contact_email' => $this->contact_email,
            'comment1' => $this->comment1,
            'comment2' => $this->comment2,
            'comment3' => $this->comment3,
            'note' => $this->note,
            'ftlb' => $this->ftlb,
            'newsletter' => '',
            'newsletter_date' => '',
            'classements' => $this->mapCategories(),
            'horaires' => $this->mapSchedules(),
            'images' => $this->mapImages(),
            'tags' => $this->mapTags(),
            'tagsObject' => $this->mapTagsObject(),
            'photos' => $this->mapPhotos(),
            'logo' => $this->mapLogo(),
            'cap' => [],
            'created_at' => $this->created_at?->toIso8601String(),
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

        return $this->categories->map(fn (Category $category): array => [
            'id' => $category->id,
            'parent' => $category->parent_id,
            'name' => $category->name,
            'slug' => $category->slug,
            'slugname' => $category->slug,
            'logo' => $category->logo,
            'logo_blanc' => $category->logo_white,
            'color' => $category->color,
            'icon' => $category->icon,
            'lvl' => 0,
            'lft' => 0,
            'rgt' => 0,
            'root' => 0,
        ])->all();
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
            'fiche_id' => $schedule->shop_id,
            'day' => $schedule->day,
            'is_open_at_lunch' => $schedule->is_open_at_lunch,
            'is_rdv' => $schedule->is_by_appointment,
            'is_closed' => $schedule->is_closed,
            'morning_start' => $schedule->morning_start,
            'morning_end' => $schedule->morning_end,
            'noon_start' => $schedule->noon_start,
            'noon_end' => $schedule->noon_end,
            'media_path' => $schedule->media_path,
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
            'image_name' => $image->file_name,
            'mime' => $image->mime_type,
            'principale' => $image->is_main,
            'updated_at' => $image->updated_at?->toIso8601String(),
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
            ->map(fn (Media $image): string => $image->file_name)
            ->values()
            ->all();
    }

    private function mapLogo(): ?string
    {
        if (! $this->relationLoaded('medias')) {
            return null;
        }

        $mainImage = $this->medias->first(fn (Media $image): bool => $image->is_main);

        return $mainImage?->file_name;
    }
}
