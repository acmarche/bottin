<?php

declare(strict_types=1);

use App\Livewire\Front\ShopIndex;
use App\Models\Shop;
use Livewire\Livewire;

it('renders the shop index page', function (): void {
    $this->get(route('shops.index'))->assertSuccessful();
});

it('lists enabled shops for the selected letter', function (): void {
    $shop = Shop::factory()->enabled()->create(['company' => 'Artisan Boulanger']);

    Livewire::test(ShopIndex::class)
        ->assertSee('Artisan Boulanger');
});

it('does not list disabled shops', function (): void {
    $disabled = Shop::factory()->disabled()->create(['company' => 'Ancien Commerce']);

    Livewire::test(ShopIndex::class)
        ->assertDontSee('Ancien Commerce');
});

it('filters shops by letter', function (): void {
    $shopA = Shop::factory()->enabled()->create(['company' => 'Alpha']);
    $shopB = Shop::factory()->enabled()->create(['company' => 'Beta']);

    Livewire::test(ShopIndex::class)
        ->assertSee('Alpha')
        ->assertDontSee('Beta')
        ->call('selectLetter', 'B')
        ->assertSee('Beta')
        ->assertDontSee('Alpha');
});
