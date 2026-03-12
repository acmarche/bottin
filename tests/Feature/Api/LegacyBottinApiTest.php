<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Media;
use App\Models\Schedule;
use App\Models\Shop;
use App\Models\Tag;

it('returns category tree with enfants', function (): void {
    $parent = Category::factory()->create(['name' => 'Parent']);
    $child = Category::factory()->create(['name' => 'Child', 'parent_id' => $parent->id]);

    $this->getJson('/api/bottin/commerces')
        ->assertSuccessful()
        ->assertJsonPath('0.name', 'Parent')
        ->assertJsonPath('0.slugname', $parent->slug)
        ->assertJsonPath('0.logo_blanc', $parent->logo_white)
        ->assertJsonPath('0.enfants.0.name', 'Child')
        ->assertJsonPath('0.enfants.0.parent', $parent->id);
});

it('returns all enabled shops', function (): void {
    $enabled = Shop::factory()->enabled()->create();
    $disabled = Shop::factory()->disabled()->create();

    $response = $this->getJson('/api/bottin/fiches')
        ->assertSuccessful();

    $ids = collect($response->json())->pluck('id');

    expect($ids)->toContain($enabled->id)
        ->not->toContain($disabled->id);
});

it('returns shops with legacy field names', function (): void {
    $shop = Shop::factory()->enabled()->create([
        'company' => 'Test Company',
        'street' => 'Rue de la Gare',
        'number' => '42',
        'postal_code' => '6900',
        'city' => 'Marche',
        'phone' => '084123456',
        'mobile' => '0471234567',
        'vat_number' => 'BE0123456789',
        'city_center' => true,
        'open_at_lunch' => true,
        'last_name' => 'Dupont',
        'first_name' => 'Jean',
    ]);

    $this->getJson("/api/bottin/fiche/{$shop->id}")
        ->assertSuccessful()
        ->assertJsonPath('societe', 'Test Company')
        ->assertJsonPath('rue', 'Rue de la Gare')
        ->assertJsonPath('numero', '42')
        ->assertJsonPath('cp', 6900)
        ->assertJsonPath('localite', 'Marche')
        ->assertJsonPath('telephone', '084123456')
        ->assertJsonPath('gsm', '0471234567')
        ->assertJsonPath('numero_tva', 'BE0123456789')
        ->assertJsonPath('centreville', true)
        ->assertJsonPath('midi', true)
        ->assertJsonPath('nom', 'Dupont')
        ->assertJsonPath('prenom', 'Jean')
        ->assertJsonPath('slugname', $shop->slug)
        ->assertJsonPath('google_plus', '')
        ->assertJsonPath('newsletter', '')
        ->assertJsonPath('cap', []);
});

it('returns shops by category', function (): void {
    $category = Category::factory()->create();
    $shopInCategory = Shop::factory()->enabled()->create();
    $shopOutside = Shop::factory()->enabled()->create();

    $shopInCategory->categories()->attach($category, ['principal' => false]);

    $response = $this->getJson("/api/bottin/fiches/rubrique/{$category->id}")
        ->assertSuccessful();

    $ids = collect($response->json())->pluck('id');

    expect($ids)->toContain($shopInCategory->id)
        ->not->toContain($shopOutside->id);
});

it('returns a shop by slug', function (): void {
    $shop = Shop::factory()->enabled()->create(['company' => 'Boulangerie Martin']);

    $this->getJson("/api/bottin/fichebyslugname/{$shop->slug}")
        ->assertSuccessful()
        ->assertJsonPath('societe', 'Boulangerie Martin')
        ->assertJsonPath('slug', $shop->slug);
});

it('returns 404 for unknown slug', function (): void {
    $this->getJson('/api/bottin/fichebyslugname/nonexistent')
        ->assertNotFound();
});

it('includes schedules with legacy field names', function (): void {
    $shop = Shop::factory()->enabled()->create();
    Schedule::factory()->create([
        'shop_id' => $shop->id,
        'day' => 1,
        'is_by_appointment' => true,
    ]);

    $this->getJson("/api/bottin/fiche/{$shop->id}")
        ->assertSuccessful()
        ->assertJsonPath('horaires.0.fiche_id', $shop->id)
        ->assertJsonPath('horaires.0.is_rdv', 1)
        ->assertJsonPath('horaires.0.day', 1);
});

it('includes images with legacy field names', function (): void {
    $shop = Shop::factory()->enabled()->create();
    Media::factory()->create([
        'shop_id' => $shop->id,
        'is_main' => true,
        'file_name' => 'photo.jpg',
    ]);

    $this->getJson("/api/bottin/fiche/{$shop->id}")
        ->assertSuccessful()
        ->assertJsonPath('images.0.fiche_id', $shop->id)
        ->assertJsonPath('images.0.principale', true)
        ->assertJsonPath('images.0.image_name', 'photo.jpg')
        ->assertJsonPath('logo', 'https://bottin.marche.be/photo.jpg')
        ->assertJsonPath('photos.0', 'https://bottin.marche.be/photo.jpg');
});

it('includes tags and tagsObject', function (): void {
    $shop = Shop::factory()->enabled()->create();
    $tag = Tag::factory()->create(['name' => 'Bio', 'slug' => 'bio']);
    $shop->tags()->attach($tag);

    $this->getJson("/api/bottin/fiche/{$shop->id}")
        ->assertSuccessful()
        ->assertJsonPath('tags.bio', 'Bio')
        ->assertJsonPath('tagsObject.bio.name', 'Bio')
        ->assertJsonPath('tagsObject.bio.slugname', 'bio');
});

it('includes categories with legacy field names', function (): void {
    $shop = Shop::factory()->enabled()->create();
    $category = Category::factory()->create(['logo_white' => 'white.png']);
    $shop->categories()->attach($category, ['principal' => false]);

    $this->getJson("/api/bottin/fiche/{$shop->id}")
        ->assertSuccessful()
        ->assertJsonPath('classements.0.description', $category->description)
        ->assertJsonPath('classements.0.slugname', $category->slug);
});
