<?php

declare(strict_types=1);

namespace App\Policies\Concerns;

use App\Enums\RolesEnum;
use App\Models\User;

trait BottinAuthorization
{
    protected function isAdmin(User $user): bool
    {
        if ($user->isAdministrator()) {
            return true;
        }

        return $user->hasOneOfThisRoles([
            RolesEnum::Admin->value,
        ]);
    }
}
