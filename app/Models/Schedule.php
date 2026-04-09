<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(ScheduleFactory::class)]
final class Schedule extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'shop_id',
        'day',
        'media_path',
        'is_by_appointment',
        'is_closed',
        'morning_start',
        'morning_end',
        'noon_start',
        'noon_end',
    ];

    /** @return BelongsTo<Shop, $this> */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_by_appointment' => 'boolean',
            'is_closed' => 'boolean',
        ];
    }

    /** @return Attribute<bool, never> */
    protected function isOpenAtLunch(): Attribute
    {
        return Attribute::get(fn (): bool => $this->morning_end === null && $this->noon_start === null);
    }
}
