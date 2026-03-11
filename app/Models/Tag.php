<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasSlug;
use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

#[UseFactory(TagFactory::class)]
final class Tag extends Model
{
    use HasFactory;
    use HasSlug;
    use Searchable;

    protected $fillable = [
        'name',
        'color',
        'icon',
        'private',
        'tag_group_id',
        'description',
    ];

    /** @return BelongsTo<TagGroup, $this> */
    public function tagGroup(): BelongsTo
    {
        return $this->belongsTo(TagGroup::class);
    }

    /** @return BelongsToMany<Shop, $this> */
    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class, 'shop_tag');
    }

    public function isPrivate(): bool
    {
        return (bool) $this->private;
    }

    /**
     * Get the name of the index associated with the model.
     */
    public function searchableAs(): string
    {
        return 'bottin_laravel_tags_index';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'private' => 'boolean',
        ];
    }

    private function slugSourceField(): string
    {
        return 'name';
    }
}
