<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TokenFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[UseFactory(TokenFactory::class)]
final class Token extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory;

    protected $rememberTokenName = '';

    protected $fillable = [
        'shop_id',
        'uuid',
        'password',
        'expire_at',
    ];

    protected $hidden = [
        'password',
    ];

    /** @return BelongsTo<Shop, $this> */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function getFilamentName(): string
    {
        return $this->shop?->company ?? 'Commerçant';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'merchant' && ! $this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->expire_at !== null && $this->expire_at->isPast();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expire_at' => 'date',
        ];
    }

    /** @return Attribute<string|null, never> */
    protected function email(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->shop?->email);
    }
}
