<?php

declare(strict_types=1);

use App\Livewire\Front\SearchResults;
use App\Models\Category;
use App\Models\Shop;
use Livewire\Livewire;

it('renders the search page', function (): void {
    $this->get(route('search'))->assertSuccessful();
});

it('searches shops by company name', function (): void {
    $shop = Shop::factory()->create(['company' => 'Boulangerie Martin']);

    Livewire::test(SearchResults::class, ['search' => 'Martin'])
        ->assertSee('Boulangerie Martin');
});

it('searches shops by city', function (): void {
    $shop = Shop::factory()->create(['company' => 'Test Shop', 'city' => 'Marche-en-Famenne']);

    Livewire::test(SearchResults::class, ['search' => 'Marche'])
        ->assertSee('Test Shop');
});

it('searches categories by name', function (): void {
    $category = Category::factory()->create(['name' => 'Alimentation']);

    Livewire::test(SearchResults::class, ['search' => 'Aliment'])
        ->assertSee('Alimentation');
});

it('requires at least 2 characters to search', function (): void {
    Shop::factory()->create(['company' => 'A Shop']);

    Livewire::test(SearchResults::class, ['search' => 'A'])
        ->assertDontSee('A Shop');
});
