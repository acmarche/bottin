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

it('can generate an api token for a user without one', function (): void {
    $user = User::factory()->create(['api_token' => null]);

    livewire(ViewUser::class, ['record' => $user->id])
        ->callAction('generateApiToken')
        ->assertNotified();

    expect($user->fresh()->api_token)->not->toBeNull();
});

it('can regenerate the api token for a user', function (): void {
    $user = User::factory()->create(['api_token' => 'initial-token']);

    livewire(ViewUser::class, ['record' => $user->id])
        ->callAction('regenerateApiToken')
        ->assertNotified();

    expect($user->fresh()->api_token)->not->toBeNull()
        ->and($user->fresh()->api_token)->not->toBe('initial-token');
});

it('can delete the api token for a user', function (): void {
    $user = User::factory()->create(['api_token' => 'initial-token']);

    livewire(ViewUser::class, ['record' => $user->id])
        ->callAction('deleteApiToken')
        ->assertNotified();

    expect($user->fresh()->api_token)->toBeNull();
});
