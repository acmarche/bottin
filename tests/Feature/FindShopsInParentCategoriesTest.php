<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Shop;

test('it finds shops classified in parent categories', function (): void {
    $parent = Category::factory()->create();
    Category::factory()->create(['parent_id' => $parent->id]);

    $shop = Shop::factory()->create();
    $shop->categories()->attach($parent->id, ['principal' => true]);

    $this->artisan('app:find-shops-in-parent-categories')
        ->expectsOutputToContain('Found 1 shop(s)')
        ->expectsOutputToContain($shop->company)
        ->assertSuccessful();
});

test('it shows info message when no shops are in parent categories', function (): void {
    $leaf = Category::factory()->create();

    $shop = Shop::factory()->create();
    $shop->categories()->attach($leaf->id, ['principal' => true]);

    $this->artisan('app:find-shops-in-parent-categories')
        ->expectsOutputToContain('No shops found in parent categories.')
        ->assertSuccessful();
});
