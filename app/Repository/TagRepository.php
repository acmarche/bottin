<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Token;
use Illuminate\Database\Eloquent\Builder;

final class TagRepository
{
    public static function listTags(Builder $query): Builder
    {
        if (auth()->user() instanceof Token) {
            return $query
                ->where('private', '=', false)
                ->with('tagGroup')
                ->orderBy('name');
        }

        return $query->with('tagGroup')->orderBy('name');
    }
}
