<?php

declare(strict_types=1);

use App\Filament\Resources\Shops\Pages\ViewShop;
use App\Filament\Resources\Shops\RelationManagers\CategoriesRelationManager;
use App\Models\Category;
use App\Models\Shop;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;

it('can render the relation manager', function () {
    $shop = Shop::factory()->create();

    livewire(CategoriesRelationManager::class, [
        'ownerRecord' => $shop,
        'pageClass' => ViewShop::class,
    ])
        ->assertOk();
});

it('can attach a leaf category via browse', function () {
    $shop = Shop::factory()->create();
    $root = Category::factory()->create(['parent_id' => null]);
    $child = Category::factory()->create(['parent_id' => $root->id]);
    $leaf = Category::factory()->create(['parent_id' => $child->id]);

    livewire(CategoriesRelationManager::class, [
        'ownerRecord' => $shop,
        'pageClass' => ViewShop::class,
    ])
        ->callAction(TestAction::make('attach')->table(), [
            'level_0' => $root->id,
            'level_1' => $child->id,
            'level_2' => $leaf->id,
            'principal' => true,
        ])
        ->assertNotified('Catégorie attachée');

    expect($shop->categories()->where('categories.id', $leaf->id)->exists())->toBeTrue();
    expect($shop->categories()->first()->pivot->principal)->toBe(1);
});

it('can attach a leaf category via search', function () {
    $shop = Shop::factory()->create();
    $root = Category::factory()->create(['parent_id' => null, 'name' => 'Root']);
    $leaf = Category::factory()->create(['parent_id' => $root->id, 'name' => 'Leaf']);

    livewire(CategoriesRelationManager::class, [
        'ownerRecord' => $shop,
        'pageClass' => ViewShop::class,
    ])
        ->callAction(TestAction::make('attach')->table(), [
            'category_search' => $leaf->id,
            'principal' => false,
        ])
        ->assertNotified('Catégorie attachée');

    expect($shop->categories()->where('categories.id', $leaf->id)->exists())->toBeTrue();
});

it('cannot attach a non-leaf category', function () {
    $shop = Shop::factory()->create();
    $root = Category::factory()->create(['parent_id' => null]);
    Category::factory()->create(['parent_id' => $root->id]);

    livewire(CategoriesRelationManager::class, [
        'ownerRecord' => $shop,
        'pageClass' => ViewShop::class,
    ])
        ->callAction(TestAction::make('attach')->table(), [
            'level_0' => $root->id,
            'principal' => false,
        ])
        ->assertNotified('Seules les catégories finales peuvent être attachées');

    expect($shop->categories()->count())->toBe(0);
});

it('cannot attach a duplicate category', function () {
    $shop = Shop::factory()->create();
    $leaf = Category::factory()->create(['parent_id' => null]);
    $shop->categories()->attach($leaf->id, ['principal' => false]);

    livewire(CategoriesRelationManager::class, [
        'ownerRecord' => $shop,
        'pageClass' => ViewShop::class,
    ])
        ->callAction(TestAction::make('attach')->table(), [
            'category_search' => $leaf->id,
            'principal' => false,
        ])
        ->assertNotified('Cette catégorie est déjà attachée');

    expect($shop->categories()->count())->toBe(1);
});
