<?php

declare(strict_types=1);

use App\Filament\Resources\TagGroups\Pages\ManageTagGroups;
use App\Models\TagGroup;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

it('can render the manage page', function () {
    livewire(ManageTagGroups::class)
        ->assertOk();
});

it('can create a tag group', function () {
    $tagGroup = TagGroup::factory()->make();

    livewire(ManageTagGroups::class)
        ->callAction('create', data: [
            'name' => $tagGroup->name,
        ])
        ->assertNotified();

    assertDatabaseHas(TagGroup::class, [
        'name' => $tagGroup->name,
    ]);
});

it('can delete a tag group', function () {
    $tagGroup = TagGroup::factory()->create();

    livewire(ManageTagGroups::class)
        ->loadTable()
        ->callTableAction(DeleteAction::class, $tagGroup)
        ->assertNotified();

    assertDatabaseMissing($tagGroup);
});

it('validates unique name on create', function () {
    $existing = TagGroup::factory()->create();

    livewire(ManageTagGroups::class)
        ->callAction('create', data: [
            'name' => $existing->name,
        ])
        ->assertHasActionErrors(['name' => ['unique']]);
});
