<?php

declare(strict_types=1);

use App\Models\History;
use App\Models\Shop;

use function Pest\Laravel\assertDatabaseHas;

it('records a history entry when a shop is created', function () {
    $shop = Shop::factory()->create(['company' => 'Acme Corp']);

    assertDatabaseHas(History::class, [
        'shop_id' => $shop->id,
        'property' => 'shop',
        'old_value' => null,
        'new_value' => 'Acme Corp',
    ]);
});

it('records a history entry when a shop is deleted', function () {
    $shop = Shop::factory()->create(['company' => 'Acme Corp']);

    $shop->delete();

    // The shop FK is set to null on delete, the company name is kept in old_value.
    assertDatabaseHas(History::class, [
        'shop_id' => null,
        'property' => 'shop',
        'old_value' => 'Acme Corp',
        'new_value' => null,
    ]);
});
