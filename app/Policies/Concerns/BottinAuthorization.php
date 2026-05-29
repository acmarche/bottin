<?php

declare(strict_types=1);

namespace App\Policies\Concerns;

use App\Enums\RolesEnum;
use App\Models\User;

trait BottinAuthorization
{
    protected function isAdmin(User $user): bool
    {
        return $user->hasRole(RolesEnum::Admin);
    }
}
