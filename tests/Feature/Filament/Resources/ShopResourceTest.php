<?php

declare(strict_types=1);

use App\Filament\Resources\Shops\Pages\CreateShop;
use App\Filament\Resources\Shops\Pages\EditShop;
use App\Filament\Resources\Shops\Pages\ListShops;
use App\Filament\Resources\Shops\Pages\ViewShop;
use App\Models\Category;
use App\Models\Locality;
use App\Models\Shop;
use App\Models\Tag;
use App\Models\Token;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

it('can render the index page', function () {
    livewire(ListShops::class)
        ->assertOk();
});

it('can render the create page', function () {
    livewire(CreateShop::class)
        ->assertOk();
});

it('can render the edit page', function () {
    $shop = Shop::factory()->create();

    livewire(EditShop::class, [
        'record' => $shop->id,
    ])
        ->assertOk();
});

it('has column', function (string $column) {
    livewire(ListShops::class)
        ->assertTableColumnExists($column);
})->with(['company', 'city', 'phone', 'email', 'created_at', 'updated_at']);

it('can sort by company', function () {
    $records = Shop::factory(5)->create();

    livewire(ListShops::class)
        ->loadTable()
        ->sortTable('company')
        ->assertCanSeeTableRecords($records->sortBy('company'), inOrder: true)
        ->sortTable('company', 'desc')
        ->assertCanSeeTableRecords($records->sortByDesc('company'), inOrder: true);
});

it('can search by company', function () {
    $records = Shop::factory(5)->create();

    $value = $records->first()->company;

    livewire(ListShops::class)
        ->loadTable()
        ->searchTable($value)
        ->assertCanSeeTableRecords($records->where('company', $value))
        ->assertCanNotSeeTableRecords($records->where('company', '!=', $value));
});

it('can create a shop and redirect to edit', function () {
    $companyName = 'New Unique Shop';

    livewire(CreateShop::class)
        ->set('company', $companyName)
        ->call('create')
        ->assertRedirect();

    assertDatabaseHas(Shop::class, [
        'company' => $companyName,
    ]);
});

it('does not create a shop with empty company name', function () {
    $countBefore = Shop::count();

    livewire(CreateShop::class)
        ->set('company', '   ')
        ->call('create');

    expect(Shop::count())->toBe($countBefore);
});

it('can update a shop', function () {
    $shop = Shop::factory()->create();
    $newData = Shop::factory()->make();

    livewire(EditShop::class, [
        'record' => $shop->id,
    ])
        ->fillForm([
            'company' => $newData->company,
        ])
        ->call('save')
        ->assertNotified();

    assertDatabaseHas(Shop::class, [
        'id' => $shop->id,
        'company' => $newData->company,
    ]);
});

it('can delete a shop', function () {
    $shop = Shop::factory()->create();

    livewire(ViewShop::class, [
        'record' => $shop->id,
    ])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseMissing($shop);
});

it('can bulk delete shops', function () {
    $shops = Shop::factory()->count(5)->create();

    livewire(ListShops::class)
        ->loadTable()
        ->assertCanSeeTableRecords($shops)
        ->selectTableRecords($shops)
        ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk())
        ->assertNotified()
        ->assertCanNotSeeTableRecords($shops);

    $shops->each(fn (Shop $shop) => assertDatabaseMissing($shop));
});

it('can render the edit page with latitude and longitude fields', function () {
    $shop = Shop::factory()->create([
        'latitude' => '50.2268',
        'longitude' => '5.3442',
    ]);

    livewire(EditShop::class, [
        'record' => $shop->id,
    ])
        ->assertOk()
        ->assertFormFieldExists('latitude')
        ->assertFormFieldExists('longitude');
});

it('can fill latitude and longitude in the form', function () {
    $shop = Shop::factory()->create();

    livewire(EditShop::class, [
        'record' => $shop->id,
    ])
        ->fillForm([
            'latitude' => '50.2268',
            'longitude' => '5.3442',
        ])
        ->assertFormSet([
            'latitude' => '50.2268',
            'longitude' => '5.3442',
        ]);
});

it('validates the form data', function (array $data, array $errors) {
    $shop = Shop::factory()->create();

    livewire(EditShop::class, [
        'record' => $shop->id,
    ])
        ->fillForm($data)
        ->call('save')
        ->assertHasFormErrors($errors)
        ->assertNotNotified();
})->with([
    '`company` is required' => [['company' => null], ['company' => 'required']],
    '`company` is max 255 characters' => [['company' => Str::random(256)], ['company' => 'max']],
]);

it('shows a warning callout when shop has no categories', function () {
    $shop = Shop::factory()->makeOne();
    $shop->saveQuietly();

    livewire(ViewShop::class, [
        'record' => $shop->id,
    ])
        ->assertOk()
        ->assertSeeText('aucune catégorie');
});

it('does not show a warning callout when shop has categories', function () {
    $shop = Shop::factory()->makeOne();
    $shop->saveQuietly();
    $shop->categories()->attach(Category::factory()->create(), ['principal' => false]);

    livewire(ViewShop::class, [
        'record' => $shop->id,
    ])
        ->assertOk()
        ->assertDontSeeText('aucune catégorie');
});

it('can generate a token for a shop without one', function () {
    $shop = Shop::factory()->makeOne();
    $shop->saveQuietly();

    livewire(ViewShop::class, [
        'record' => $shop->id,
    ])
        ->callAction('generateToken')
        ->assertNotified();

    expect($shop->fresh()->token)->not->toBeNull();
});

it('can regenerate a token for a shop', function () {
    $shop = Shop::factory()->makeOne();
    $shop->saveQuietly();
    $oldToken = Token::factory()->create(['shop_id' => $shop->id]);
    $oldUuid = $oldToken->uuid;

    livewire(ViewShop::class, [
        'record' => $shop->id,
    ])
        ->callAction('regenerateToken')
        ->assertNotified();

    $newToken = $shop->fresh()->token;
    expect($newToken)->not->toBeNull()
        ->and($newToken->uuid)->not->toBe($oldUuid);
});

it('can delete a token for a shop', function () {
    $shop = Shop::factory()->makeOne();
    $shop->saveQuietly();
    Token::factory()->create(['shop_id' => $shop->id]);

    livewire(ViewShop::class, [
        'record' => $shop->id,
    ])
        ->callAction('deleteToken')
        ->assertNotified();

    expect($shop->fresh()->token)->toBeNull();
});

it('can filter shops without categories', function () {
    $shopWithCategory = Shop::factory()->create();
    $shopWithCategory->categories()->attach(Category::factory()->create(), ['principal' => true]);

    $shopWithoutCategory = Shop::factory()->create();

    livewire(ListShops::class)
        ->loadTable()
        ->filterTable('without_category', true)
        ->assertCanSeeTableRecords([$shopWithoutCategory])
        ->assertCanNotSeeTableRecords([$shopWithCategory]);
});

it('can filter shops by tag', function () {
    $tag = Tag::factory()->create();
    $shopWithTag = Shop::factory()->create();
    $shopWithTag->tags()->attach($tag);

    $shopWithoutTag = Shop::factory()->create();

    livewire(ListShops::class)
        ->loadTable()
        ->filterTable('tags', [$tag->id])
        ->assertCanSeeTableRecords([$shopWithTag])
        ->assertCanNotSeeTableRecords([$shopWithoutTag]);
});

it('can filter shops by locality', function () {
    Locality::factory()->create(['name' => 'Marloie']);
    $shopInLocality = Shop::factory()->create(['city' => 'Marloie']);
    $shopOutside = Shop::factory()->create(['city' => 'Namur']);

    livewire(ListShops::class)
        ->loadTable()
        ->filterTable('city', 'Marloie')
        ->assertCanSeeTableRecords([$shopInLocality])
        ->assertCanNotSeeTableRecords([$shopOutside]);
});
