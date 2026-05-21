<?php

declare(strict_types=1);

use App\Models\Shop;

it('exposes indexable meta tags on the shop detail page', function (): void {
    $shop = Shop::factory()->create([
        'company' => 'Ma Boulangerie',
        'comment1' => 'La meilleure boulangerie artisanale de la région.',
    ]);

    $response = $this->get(route('shop.show', $shop));

    $response->assertSuccessful()
        ->assertSee('<meta name="robots" content="index, follow">', false)
        ->assertSee('<meta name="description"', false)
        ->assertSee('boulangerie artisanale', false)
        ->assertSee('<link rel="canonical" href="'.route('shop.show', $shop).'">', false)
        ->assertSee('<meta property="og:title" content="Ma Boulangerie">', false);
});

it('embeds LocalBusiness structured data on the shop detail page', function (): void {
    $shop = Shop::factory()->create([
        'company' => 'Ma Boulangerie',
        'phone' => '084123456',
        'city' => 'Marche',
    ]);

    $response = $this->get(route('shop.show', $shop));

    $response->assertSuccessful()
        ->assertSee('application/ld+json', false)
        ->assertSee('"@type":"LocalBusiness"', false)
        ->assertSee('"name":"Ma Boulangerie"', false);
});

it('marks the search results page as noindex', function (): void {
    $this->get(route('search'))
        ->assertSuccessful()
        ->assertSee('<meta name="robots" content="noindex, follow">', false);
});

it('serves an XML sitemap listing shop pages', function (): void {
    $shop = Shop::factory()->create();

    $response = $this->get(route('sitemap'));

    $response->assertSuccessful();
    expect($response->headers->get('Content-Type'))->toContain('application/xml');
    $response->assertSee('<urlset', false)
        ->assertSee(route('shop.show', $shop), false)
        ->assertSee(route('home'), false);
});

it('serves a robots.txt referencing the sitemap', function (): void {
    $this->get(route('robots'))
        ->assertSuccessful()
        ->assertSee('User-agent: *')
        ->assertSee('Sitemap: '.route('sitemap'));
});
