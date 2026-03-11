<?php

declare(strict_types=1);

use App\Filament\Resources\PointOfSales\Pages\ManagePointOfSales;
use App\Models\PointOfSale;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

it('can render the manage page', function () {
    livewire(ManagePointOfSales::class)
        ->assertOk();
});

it('can create a point of sale', function () {
    $pointOfSale = PointOfSale::factory()->make();

    livewire(ManagePointOfSales::class)
        ->callAction('create', data: [
            'name' => $pointOfSale->name,
        ])
        ->assertNotified();

    assertDatabaseHas(PointOfSale::class, [
        'name' => $pointOfSale->name,
    ]);
});

it('can delete a point of sale', function () {
    $pointOfSale = PointOfSale::factory()->create();

    livewire(ManagePointOfSales::class)
        ->loadTable()
        ->callTableAction(DeleteAction::class, $pointOfSale)
        ->assertNotified();

    assertDatabaseMissing($pointOfSale);
});
