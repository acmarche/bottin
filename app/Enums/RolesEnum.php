<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RolesEnum: string implements HasColor, HasLabel
{
    case Admin = 'ROLE_ADMIN';
    case Api = 'ROLE_API';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Api => 'API',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Api => 'info',
        };
    }
}
