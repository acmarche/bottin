<?php

declare(strict_types=1);

use App\Livewire\Front\CategoryShow;
use App\Models\Category;
use App\Models\Shop;
use Livewire\Livewire;

it('renders the category page', function (): void {
    $category = Category::factory()->create();

    $this->get(route('category.show', $category))->assertSuccessful();
});

it('displays the category name and description', function (): void {
    $category = Category::factory()->create([
        'name' => 'Alimentation',
        'description' => 'Tous les commerces alimentaires',
    ]);

    Livewire::test(CategoryShow::class, ['category' => $category])
        ->assertSee('Alimentation')
        ->assertSee('Tous les commerces alimentaires');
});

it('displays child categories', function (): void {
    $parent = Category::factory()->create(['name' => 'Services']);
    $child = Category::factory()->create(['name' => 'Banques', 'parent_id' => $parent->id]);

    Livewire::test(CategoryShow::class, ['category' => $parent])
        ->assertSee('Banques');
});

it('lists enabled shops in the category', function (): void {
    $category = Category::factory()->create();
    $shop = Shop::factory()->create(['company' => 'Boulangerie Test']);
    $shop->categories()->attach($category, ['principal' => true]);

    Livewire::test(CategoryShow::class, ['category' => $category])
        ->assertSee('Boulangerie Test');
});

it('lists shops from sub-categories', function (): void {
    $parent = Category::factory()->create();
    $child = Category::factory()->create(['parent_id' => $parent->id]);
    $shop = Shop::factory()->create(['company' => 'Sub Shop']);
    $shop->categories()->attach($child, ['principal' => true]);

    Livewire::test(CategoryShow::class, ['category' => $parent])
        ->assertSee('Sub Shop');
});
