<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    abstract private function slugSourceField(): string;

    public static function bootHasSlug(): void
    {
        static::creating(function ($model): void {
            $model->slug = $model->generateUniqueSlug();
        });

        static::updating(function ($model): void {
            $model->slug = $model->generateUniqueSlug();
        });
    }

    private function generateUniqueSlug(): string
    {
        $source = $this->{$this->slugSourceField()} ?? '';
        $slug = Str::slug($source);

        if ($slug === '') {
            $slug = 'n-a';
        }

        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugAlreadyExists($slug)) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function slugAlreadyExists(string $slug): bool
    {
        $query = static::where('slug', $slug);

        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        return $query->exists();
    }
}
