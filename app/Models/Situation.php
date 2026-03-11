<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SituationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[UseFactory(SituationFactory::class)]
final class Situation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    /** @return BelongsToMany<Shop, $this> */
    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class, 'shop_situation');
    }
}
