<?php

declare(strict_types=1);

use App\Livewire\Front\ShopDetail;
use App\Models\Category;
use App\Models\Schedule;
use App\Models\Shop;
use App\Models\Tag;
use App\Models\TagGroup;
use Livewire\Livewire;

it('renders the shop detail page', function (): void {
    $shop = Shop::factory()->create();

    $this->get(route('shop.show', $shop))->assertSuccessful();
});

it('displays shop information', function (): void {
    $shop = Shop::factory()->create([
        'company' => 'Ma Boulangerie',
        'street' => 'Rue Haute',
        'number' => '12',
        'city' => 'Marche',
        'phone' => '084123456',
        'email' => 'contact@boulangerie.be',
    ]);

    Livewire::test(ShopDetail::class, ['shop' => $shop])
        ->assertSee('Ma Boulangerie')
        ->assertSee('Rue Haute')
        ->assertSee('Marche')
        ->assertSee('084123456')
        ->assertSee('contact@boulangerie.be');
});

it('displays categories as badges', function (): void {
    $shop = Shop::factory()->create();
    $category = Category::factory()->create(['name' => 'Alimentation']);
    $shop->categories()->attach($category, ['principal' => true]);

    Livewire::test(ShopDetail::class, ['shop' => $shop])
        ->assertSee('Alimentation');
});

it('displays public tags only', function (): void {
    $shop = Shop::factory()->create();
    $group = TagGroup::factory()->create();
    $publicTag = Tag::factory()->create(['name' => 'Bio', 'private' => false, 'tag_group_id' => $group->id]);
    $privateTag = Tag::factory()->create(['name' => 'Internal', 'private' => true, 'tag_group_id' => $group->id]);
    $shop->tags()->attach([$publicTag->id, $privateTag->id]);

    Livewire::test(ShopDetail::class, ['shop' => $shop])
        ->assertSee('Bio')
        ->assertDontSee('Internal');
});

it('displays schedules', function (): void {
    $shop = Shop::factory()->create();
    Schedule::factory()->create([
        'shop_id' => $shop->id,
        'day' => 1,
        'morning_start' => '08:00',
        'morning_end' => '12:00',
    ]);

    Livewire::test(ShopDetail::class, ['shop' => $shop])
        ->assertSee('Lundi')
        ->assertSee('08:00');
});

it('displays service badges when applicable', function (): void {
    $shop = Shop::factory()->create();
    $pmrTag = Tag::factory()->create(['name' => 'Pmr', 'private' => false]);
    $clickCollectTag = Tag::factory()->create(['name' => 'Click & Collect', 'private' => false]);
    $ecommerceTag = Tag::factory()->create(['name' => 'Ecommerce', 'private' => false]);
    $shop->tags()->attach([$pmrTag->id, $clickCollectTag->id, $ecommerceTag->id]);

    Livewire::test(ShopDetail::class, ['shop' => $shop])
        ->assertSee('PMR')
        ->assertSee('Click & Collect')
        ->assertSee('E-commerce');
});
