<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\HistoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(HistoryFactory::class)]
final class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'made_by',
        'property',
        'old_value',
        'new_value',
    ];

    /** @return BelongsTo<Shop, $this> */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
