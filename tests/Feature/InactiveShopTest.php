<?php

declare(strict_types=1);

use App\Filament\Resources\Shops\Pages\EditShop;
use App\Filament\Resources\Shops\Pages\ListShops;
use App\Livewire\Front\CategoryShow;
use App\Livewire\Front\SearchResults;
use App\Livewire\Front\ShopIndex;
use App\Livewire\Front\TagShow;
use App\Models\Category;
use App\Models\Shop;
use App\Models\Tag;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

it('creates shops as active by default', function (): void {
    $shop = Shop::factory()->create();

    expect($shop->refresh()->is_active)->toBeTrue();
});

it('scopes queries to active shops', function (): void {
    $active = Shop::factory()->create();
    $inactive = Shop::factory()->inactive()->create();

    $ids = Shop::query()->active()->pluck('id');

    expect($ids)->toContain($active->id)->not->toContain($inactive->id);
});

it('is only searchable when active', function (): void {
    expect(Shop::factory()->make()->shouldBeSearchable())->toBeTrue()
        ->and(Shop::factory()->inactive()->make()->shouldBeSearchable())->toBeFalse();
});

it('hides inactive shops from the category page', function (): void {
    $category = Category::factory()->create();
    $active = Shop::factory()->create(['company' => 'Boulangerie Active']);
    $inactive = Shop::factory()->inactive()->create(['company' => 'Boulangerie Fermee']);
    $category->shops()->attach([$active->id, $inactive->id], ['principal' => true]);

    Livewire::test(CategoryShow::class, ['category' => $category])
        ->assertSee('Boulangerie Active')
        ->assertDontSee('Boulangerie Fermee');
});

it('hides inactive shops from the tag page', function (): void {
    $tag = Tag::factory()->create(['private' => false]);
    $active = Shop::factory()->create(['company' => 'Boucherie Active']);
    $inactive = Shop::factory()->inactive()->create(['company' => 'Boucherie Fermee']);
    $tag->shops()->attach([$active->id, $inactive->id]);

    Livewire::test(TagShow::class, ['tag' => $tag])
        ->assertSee('Boucherie Active')
        ->assertDontSee('Boucherie Fermee');
});

it('hides inactive shops from the A-Z index', function (): void {
    Shop::factory()->create(['company' => 'Alpha Active']);
    Shop::factory()->inactive()->create(['company' => 'Alpha Fermee']);

    Livewire::test(ShopIndex::class, ['letter' => 'A'])
        ->assertSee('Alpha Active')
        ->assertDontSee('Alpha Fermee');
});

it('hides inactive shops from search results', function (): void {
    Shop::factory()->create(['company' => 'Librairie Active']);
    Shop::factory()->inactive()->create(['company' => 'Librairie Fermee']);

    Livewire::test(SearchResults::class, ['search' => 'Librairie'])
        ->assertSee('Librairie Active')
        ->assertDontSee('Librairie Fermee');
});

it('returns 404 for the detail page and pdf export of an inactive shop', function (): void {
    $shop = Shop::factory()->inactive()->create();

    $this->get(route('shop.show', $shop))->assertNotFound();
    $this->get(route('export.shop', $shop))->assertNotFound();
});

it('excludes inactive shops from the sitemap', function (): void {
    $active = Shop::factory()->create();
    $inactive = Shop::factory()->inactive()->create();

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee(route('shop.show', $active), false)
        ->assertDontSee(route('shop.show', $inactive), false);
});

it('excludes inactive shops from the api list endpoints', function (): void {
    $category = Category::factory()->create();
    $active = Shop::factory()->create();
    $inactive = Shop::factory()->inactive()->create();
    $category->shops()->attach([$active->id, $inactive->id], ['principal' => true]);

    $allIds = collect($this->getJson('/api/bottin/fiches')->assertSuccessful()->json())->pluck('id');
    $categoryIds = collect($this->getJson("/api/bottin/fiches/category/{$category->id}")->assertSuccessful()->json())->pluck('id');

    expect($allIds)->toContain($active->id)->not->toContain($inactive->id)
        ->and($categoryIds)->toContain($active->id)->not->toContain($inactive->id);
});

it('returns 404 from the api for a single inactive shop', function (): void {
    $shop = Shop::factory()->inactive()->create();

    $this->getJson("/api/bottin/fiche/{$shop->id}")->assertNotFound();
    $this->getJson("/api/bottin/fichebyslugname/{$shop->slug}")->assertNotFound();
});

it('can deactivate a shop from the admin form', function (): void {
    $shop = Shop::factory()->create();

    livewire(EditShop::class, ['record' => $shop->id])
        ->fillForm(['is_active' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Shop::class, ['id' => $shop->id, 'is_active' => false]);
});

it('shows only active shops by default in the admin table', function (): void {
    $active = Shop::factory()->create();
    $inactive = Shop::factory()->inactive()->create();

    livewire(ListShops::class)
        ->loadTable()
        ->assertCanSeeTableRecords([$active])
        ->assertCanNotSeeTableRecords([$inactive]);
});

it('can filter shops by active state in the admin table', function (): void {
    $active = Shop::factory()->create();
    $inactive = Shop::factory()->inactive()->create();

    livewire(ListShops::class)
        ->loadTable()
        ->filterTable('is_active', false)
        ->assertCanSeeTableRecords([$inactive])
        ->assertCanNotSeeTableRecords([$active]);
});
