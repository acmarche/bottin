<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\RolesEnum;
use App\Repository\UserRepository;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

final class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                CheckboxList::make('roles')
                    ->label('Rôles')
                    ->options(RolesEnum::class),
            ]);
    }

    public static function add(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('username')
                    ->label('Nom')
                    ->options(UserRepository::listUsersFromLdapForSelect())
                    ->searchable(),
            ]);
    }
}
