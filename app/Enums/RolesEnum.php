<?php

declare(strict_types=1);

namespace App\Enums;

enum RolesEnum: string
{
    case Admin = 'ROLE_ADMIN';
    case Api = 'ROLE_API';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Api => 'API',
        };
    }
}
