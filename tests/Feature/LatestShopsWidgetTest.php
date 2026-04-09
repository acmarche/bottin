<?php

declare(strict_types=1);

use App\Filament\Widgets\LatestShops;
use App\Models\Shop;

use function Pest\Livewire\livewire;

it('can render the latest shops widget', function () {
    livewire(LatestShops::class)
        ->assertOk();
});

it('displays the latest shops', function () {
    $shops = Shop::factory()->count(3)->create();

    livewire(LatestShops::class)
        ->assertCanSeeTableRecords($shops);
});
