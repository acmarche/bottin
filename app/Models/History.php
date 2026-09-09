<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\HistoryActionEnum;
use Database\Factories\HistoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(HistoryFactory::class)]
final class History extends Model
{
    use HasFactory;

    /**
     * Property used by the shop lifecycle entries (creation / deletion),
     * as opposed to the entries tracking a single changed field.
     */
    public const LIFECYCLE_PROPERTY = 'shop';

    protected $fillable = [
        'shop_id',
        'made_by',
        'property',
        'old_value',
        'new_value',
    ];

    public function action(): HistoryActionEnum
    {
        if ($this->property !== self::LIFECYCLE_PROPERTY) {
            return HistoryActionEnum::Updated;
        }

        return $this->new_value !== null ? HistoryActionEnum::Created : HistoryActionEnum::Deleted;
    }

    /**
     * Company name of the related shop, falling back to the name recorded in the
     * lifecycle entry when the shop itself has since been deleted.
     */
    public function shopName(): ?string
    {
        if ($this->shop !== null) {
            return $this->shop->company;
        }

        if ($this->property !== self::LIFECYCLE_PROPERTY) {
            return null;
        }

        return $this->new_value ?? $this->old_value;
    }

    /** @return BelongsTo<Shop, $this> */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
