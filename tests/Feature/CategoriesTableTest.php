<?php

declare(strict_types=1);

use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use App\Models\Shop;

use function Pest\Livewire\livewire;

test('without_shop filter shows leaf categories without shops', function (): void {
    $parent = Category::factory()->create();
    $leafWithShop = Category::factory()->create(['parent_id' => $parent->id]);
    $leafWithoutShop = Category::factory()->create(['parent_id' => $parent->id]);

    $shop = Shop::factory()->create();
    $leafWithShop->shops()->attach($shop->id, ['principal' => true]);

    livewire(ListCategories::class)
        ->filterTable('without_shop', true)
        ->loadTable()
        ->assertCanSeeTableRecords([$leafWithoutShop])
        ->assertCanNotSeeTableRecords([$parent, $leafWithShop]);
});

test('without_shop filter false shows leaf categories with shops', function (): void {
    $parent = Category::factory()->create();
    $leafWithShop = Category::factory()->create(['parent_id' => $parent->id]);
    $leafWithoutShop = Category::factory()->create(['parent_id' => $parent->id]);

    $shop = Shop::factory()->create();
    $leafWithShop->shops()->attach($shop->id, ['principal' => true]);

    livewire(ListCategories::class)
        ->filterTable('without_shop', false)
        ->loadTable()
        ->assertCanSeeTableRecords([$leafWithShop])
        ->assertCanNotSeeTableRecords([$parent, $leafWithoutShop]);
});

test('default view shows only root categories', function (): void {
    $parent = Category::factory()->create();
    $child = Category::factory()->create(['parent_id' => $parent->id]);

    livewire(ListCategories::class)
        ->loadTable()
        ->assertCanSeeTableRecords([$parent])
        ->assertCanNotSeeTableRecords([$child]);
});
