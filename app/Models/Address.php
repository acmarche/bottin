<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasSlug;
use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(AddressFactory::class)]
final class Address extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'name',
        'street',
        'number',
        'postal_code',
        'city',
        'longitude',
        'latitude',
    ];

    /** @return HasMany<Shop, $this> */
    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    private function slugSourceField(): string
    {
        return 'name';
    }
}
