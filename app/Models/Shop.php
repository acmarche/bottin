<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasSlug;
use App\Observers\ShopObserver;
use Database\Factories\ShopFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[UseFactory(ShopFactory::class)]
#[ObservedBy([ShopObserver::class])]
final class Shop extends Model implements HasMedia
{
    use HasFactory;
    use HasSlug;
    use InteractsWithMedia;
    use Searchable;

    protected $fillable = [
        'address_id',
        'company',
        'street',
        'number',
        'postal_code',
        'city',
        'phone',
        'phone_other',
        'mobile',
        'website',
        'email',
        'facebook',
        'twitter',
        'instagram',
        'tiktok',
        'youtube',
        'linkedin',
        'longitude',
        'latitude',
        'vat_number',
        'function',
        'civility',
        'last_name',
        'first_name',
        'contact_street',
        'contact_number',
        'contact_postal_code',
        'contact_city',
        'contact_phone',
        'contact_phone_other',
        'contact_mobile',
        'contact_email',
        'admin_function',
        'admin_civility',
        'admin_last_name',
        'admin_first_name',
        'admin_phone',
        'admin_phone_other',
        'admin_mobile',
        'admin_email',
        'comment1',
        'comment2',
        'comment3',
        'note',
        'user',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->fit(Fit::Crop, 640, 360)
            ->withResponsiveImages()
            ->nonQueued();

        $this
            ->addMediaConversion('detail')
            ->fit(Fit::Contain, 800, 600)
            ->withResponsiveImages()
            ->nonQueued();
    }

    /** @return BelongsTo<Address, $this> */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /** @return HasMany<Schedule, $this> */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /** @return HasMany<History, $this> */
    public function histories(): HasMany
    {
        return $this->hasMany(History::class);
    }

    /** @return HasOne<Token, $this> */
    public function token(): HasOne
    {
        return $this->hasOne(Token::class);
    }

    /** @return BelongsToMany<Category, $this> */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_shop')
            ->withPivot('principal');
    }

    /** @return BelongsToMany<Situation, $this> */
    public function situations(): BelongsToMany
    {
        return $this->belongsToMany(Situation::class, 'shop_situation');
    }

    /** @return BelongsToMany<Tag, $this> */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'shop_tag');
    }

    /**
     * Get the name of the index associated with the model.
     */
    public function searchableAs(): string
    {
        return 'bottin_laravel_shops_index';
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $mainImage = $this->getFirstMedia('images', fn ($m): bool => (bool) $m->getCustomProperty('is_main') === true)
                     ?? $this->getFirstMedia('images');

        return [
            'id' => $this->id,
            'company' => $this->company,
            'street' => $this->street,
            'number' => $this->number,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'phone' => $this->phone,
            'phone_other' => $this->phone_other,
            'mobile' => $this->mobile,
            'website' => $this->website,
            'email' => $this->email,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'city_center' => $this->hasTag('Centre ville'),
            'open_at_lunch' => $this->hasTag('Ouvert le midi'),
            'pmr' => $this->hasTag('Pmr'),
            'vat_number' => $this->vat_number,
            'function' => $this->function,
            'civility' => $this->civility,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'contact_street' => $this->contact_street,
            'contact_number' => $this->contact_number,
            'contact_postal_code' => $this->contact_postal_code,
            'contact_city' => $this->contact_city,
            'contact_phone' => $this->contact_phone,
            'contact_phone_other' => $this->contact_phone_other,
            'contact_mobile' => $this->contact_mobile,
            'contact_email' => $this->contact_email,
            'comment1' => $this->comment1,
            'comment2' => $this->comment2,
            'comment3' => $this->comment3,
            'slug' => $this->slug,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'instagram' => $this->instagram,
            'tiktok' => $this->tiktok,
            'youtube' => $this->youtube,
            'linkedin' => $this->linkedin,
            'updated_at' => $this->updated_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'image' => $mainImage?->getUrl(),
            'tags' => $this->tags->pluck('name')->all(),
            'tags_object' => $this->tags->map(fn (Tag $tag): array => [
                'id' => $tag->id,
                'name' => $tag->name,
                'description' => $tag->description,
                'group' => $tag->tagGroup?->name,
                'color' => $tag->color,
                'icon' => $tag->icon,
                'private' => $tag->private,
            ])->all(),
            'type' => 'fiche',
            'categories' => $this->categories->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'logo' => $category->logo ?? '',
                'icon' => $category->icon,
                'slug' => $category->slug,
                'parent_id' => $category->parent_id,
            ])->all(),
            '_geo' => ($this->latitude && $this->longitude) ? [
                'lat' => $this->latitude,
                'lng' => $this->longitude,
            ] : null,
        ];
    }

    public function hasTag(string $name): bool
    {
        return $this->tags->contains('name', $name);
    }

    protected static function booted(): void
    {
        self::creating(function (self $model) {
            if (Auth::check()) {
                $user = Auth::user();
                $model->user = $user->username;
            }
        });
    }

    /**
     * Eager load relations when making all models searchable.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with(['tags.tagGroup', 'categories', 'media']);
    }

    private function slugSourceField(): string
    {
        return 'company';
    }
}
