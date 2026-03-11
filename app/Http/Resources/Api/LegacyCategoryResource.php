<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Category */
final class LegacyCategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'slugname' => $this->slug,
            'description' => $this->description,
            'mobile' => $this->mobile,
            'logo' => $this->logo,
            'logo_blanc' => $this->logo_white,
            'color' => $this->color,
            'icon' => $this->icon,
            'lvl' => 0,
            'lft' => 0,
            'rgt' => 0,
            'root' => 0,
            'enfants' => self::collection($this->whenLoaded('children')),
        ];
    }
}
