<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TagGroupFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(TagGroupFactory::class)]
final class TagGroup extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'private',
    ];

    public function isPrivate(): bool
    {
        return (bool) $this->private;
    }

    /** @return HasMany<Tag, $this> */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
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
}
