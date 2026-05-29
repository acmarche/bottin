<?php

declare(strict_types=1);

use App\Enums\RolesEnum;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;

it('displays user roles in the table', function (): void {
    $user = User::factory()->create(['roles' => [RolesEnum::Admin, RolesEnum::Api]]);

    livewire(ListUsers::class)
        ->loadTable()
        ->assertCanSeeTableRecords([$user])
        ->assertSee(RolesEnum::Admin->getLabel())
        ->assertSee(RolesEnum::Api->getLabel());
});

it('displays user roles on the view page', function (): void {
    $user = User::factory()->create(['roles' => [RolesEnum::Admin]]);

    livewire(ViewUser::class, ['record' => $user->id])
        ->assertOk();
});

it('loads the current roles into the edit action form', function (): void {
    $user = User::factory()->create(['roles' => [RolesEnum::Admin]]);

    livewire(ListUsers::class)
        ->mountAction(TestAction::make('edit')->table($user))
        ->assertSchemaStateSet([
            'roles' => [RolesEnum::Admin],
        ]);
});

it('updates user roles through the edit action', function (): void {
    $user = User::factory()->create(['roles' => [RolesEnum::Admin]]);

    livewire(ListUsers::class)
        ->callAction(TestAction::make('edit')->table($user), [
            'roles' => [RolesEnum::Admin->value, RolesEnum::Api->value],
        ])
        ->assertNotified();

    $user->refresh();

    expect($user->hasRole(RolesEnum::Admin))->toBeTrue()
        ->and($user->hasRole(RolesEnum::Api))->toBeTrue();
});
