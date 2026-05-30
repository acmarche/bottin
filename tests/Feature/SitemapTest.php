<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Shop;

use function Pest\Laravel\get;

it('returns a valid xml sitemap', function () {
    get(route('sitemap'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml');
});

it('lists shops and categories with correct urls', function () {
    $category = Category::factory()->create();
    $shop = Shop::factory()->create();

    get(route('sitemap'))
        ->assertOk()
        ->assertSee(route('category.show', $category), false)
        ->assertSee(route('shop.show', $shop), false)
        ->assertSee('/categorie/'.$category->slug, false);
});
