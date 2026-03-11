<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasSlug;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;

#[UseFactory(CategoryFactory::class)]
final class Category extends Model
{
    use HasFactory;
    use HasSlug;
    use Searchable;

    protected $fillable = [
        'parent_id',
        'name',
        'description',
        'mobile',
        'logo',
        'logo_white',
        'color',
        'icon',
    ];

    /** @return BelongsTo<self, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<self, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /** @return BelongsToMany<Shop, $this> */
    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class, 'category_shop')
            ->withPivot('principal');
    }

    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    public function fullPath(): string
    {
        $segments = [$this->name];
        $current = $this;

        while ($current->parent !== null) {
            $current = $current->parent;
            $segments[] = $current->name;
        }

        return implode(' > ', array_reverse($segments));
    }

    /** @return Collection<int, int> */
    public function descendantsAndSelfIds(): Collection
    {
        /** @var Collection<int, int> $ids */
        $ids = collect([$this->id]);
        $parentIds = $ids;

        while ($parentIds->isNotEmpty()) {
            $parentIds = self::query()
                ->whereIn('parent_id', $parentIds)
                ->pluck('id');

            $ids = $ids->merge($parentIds);
        }

        return $ids->unique()->values();
    }

    /** @param Builder<self> $query */
    public function scopeLeaves(Builder $query): void
    {
        $query->whereDoesntHave('children');
    }

    /**
     * Get the name of the index associated with the model.
     */
    public function searchableAs(): string
    {
        return 'bottin_laravel_categories_index';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mobile' => 'boolean',
        ];
    }

    private function slugSourceField(): string
    {
        return 'name';
    }
}
