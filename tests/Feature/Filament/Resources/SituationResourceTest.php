<?php

declare(strict_types=1);

use App\Filament\Resources\Situations\Pages\CreateSituation;
use App\Filament\Resources\Situations\Pages\EditSituation;
use App\Filament\Resources\Situations\Pages\ListSituations;
use App\Models\Situation;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

it('can render the index page', function () {
    livewire(ListSituations::class)
        ->assertOk();
});

it('can render the create page', function () {
    livewire(CreateSituation::class)
        ->assertOk();
});

it('can render the edit page', function () {
    $situation = Situation::factory()->create();

    livewire(EditSituation::class, [
        'record' => $situation->id,
    ])
        ->assertOk();
});

it('can create a situation', function () {
    $situation = Situation::factory()->make();

    livewire(CreateSituation::class)
        ->fillForm([
            'name' => $situation->name,
        ])
        ->call('create')
        ->assertNotified();

    assertDatabaseHas(Situation::class, [
        'name' => $situation->name,
    ]);
});

it('can update a situation', function () {
    $situation = Situation::factory()->create();
    $newData = Situation::factory()->make();

    livewire(EditSituation::class, [
        'record' => $situation->id,
    ])
        ->fillForm([
            'name' => $newData->name,
        ])
        ->call('save')
        ->assertNotified();

    assertDatabaseHas(Situation::class, [
        'id' => $situation->id,
        'name' => $newData->name,
    ]);
});

it('can delete a situation', function () {
    $situation = Situation::factory()->create();

    livewire(EditSituation::class, [
        'record' => $situation->id,
    ])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseMissing($situation);
});
