<?php

declare(strict_types=1);

use App\Filament\Resources\Localities\Pages\ManageLocalities;
use App\Models\Locality;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

it('can render the manage page', function () {
    livewire(ManageLocalities::class)
        ->assertOk();
});

it('can create a locality', function () {
    $locality = Locality::factory()->make();

    livewire(ManageLocalities::class)
        ->callAction('create', data: [
            'name' => $locality->name,
        ])
        ->assertNotified();

    assertDatabaseHas(Locality::class, [
        'name' => $locality->name,
    ]);
});

it('can delete a locality', function () {
    $locality = Locality::factory()->create();

    livewire(ManageLocalities::class)
        ->loadTable()
        ->callTableAction(DeleteAction::class, $locality)
        ->assertNotified();

    assertDatabaseMissing($locality);
});
