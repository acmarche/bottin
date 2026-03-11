<?php

declare(strict_types=1);

use App\Filament\Widgets\DataQualityOverview;
use App\Models\Category;
use App\Models\Shop;
use Livewire\Livewire;

test('it displays shops without categories count', function (): void {
    Shop::factory()->count(3)->create();

    $shopWithCategory = Shop::factory()->create();
    $category = Category::factory()->create();
    $shopWithCategory->categories()->attach($category->id, ['principal' => true]);

    Livewire::test(DataQualityOverview::class)
        ->assertSeeText('3');
});

test('it displays leaf categories without shops count', function (): void {
    $parent = Category::factory()->create();
    Category::factory()->count(2)->create(['parent_id' => $parent->id]);

    $categoryWithShop = Category::factory()->create(['parent_id' => $parent->id]);
    $shop = Shop::factory()->create();
    $shop->categories()->attach($categoryWithShop->id, ['principal' => true]);

    Livewire::test(DataQualityOverview::class)
        ->assertSeeText('2');
});

test('it displays duplicate shops count', function (): void {
    Shop::factory()->count(2)->create(['company' => 'Duplicate Corp', 'postal_code' => '6900']);
    Shop::factory()->count(3)->create(['company' => 'Another Duplicate', 'postal_code' => '5000']);
    Shop::factory()->create(['company' => 'Duplicate Corp', 'postal_code' => '5000']);
    Shop::factory()->create(['company' => 'Unique Business', 'postal_code' => '6900']);

    Livewire::test(DataQualityOverview::class)
        ->assertSeeText('2');
});

test('it can mount the duplicates action', function (): void {
    Shop::factory()->count(2)->create(['company' => 'Duplicate Corp', 'postal_code' => '6900']);
    Shop::factory()->create(['company' => 'Unique Business', 'postal_code' => '5000']);

    Livewire::test(DataQualityOverview::class)
        ->mountAction('showDuplicates')
        ->assertActionMounted('showDuplicates');
});
