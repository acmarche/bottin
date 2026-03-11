<?php

declare(strict_types=1);

use App\Exports\CategoryShopsExport;
use App\Filament\Resources\Categories\Pages\ViewCategory;
use App\Filament\Resources\Categories\RelationManagers\ShopsRelationManager;
use App\Models\Category;
use App\Models\Shop;
use Maatwebsite\Excel\Facades\Excel;

use function Pest\Livewire\livewire;

beforeEach(function () {
    auth()->user()->update(['username' => 'testuser']);
});

it('returns descendant and self ids from materialized path', function () {
    $root = Category::factory()->create();
    $child = Category::factory()->create([
        'parent_id' => $root->id,
    ]);
    $grandchild = Category::factory()->create([
        'parent_id' => $child->id,
    ]);
    $unrelated = Category::factory()->create();

    $ids = $root->descendantsAndSelfIds();

    expect($ids)
        ->toContain($root->id)
        ->toContain($child->id)
        ->toContain($grandchild->id)
        ->not->toContain($unrelated->id);
});

it('renders the view category page with export action', function () {
    $category = Category::factory()->create();

    livewire(ViewCategory::class, ['record' => $category->id])
        ->assertOk()
        ->assertActionExists('exportXls');
});

it('lists shops in the relation manager', function () {
    $category = Category::factory()->create();
    $shop = Shop::factory()->create(['company' => 'Boulangerie Test']);
    $shop->categories()->attach($category->id, ['principal' => true]);

    livewire(ShopsRelationManager::class, [
        'ownerRecord' => $category,
        'pageClass' => ViewCategory::class,
    ])
        ->loadTable()
        ->assertCanSeeTableRecords([$shop]);
});

it('collects shops from descendant categories', function () {
    $root = Category::factory()->create();
    $child = Category::factory()->create([
        'parent_id' => $root->id,
    ]);

    $shopInRoot = Shop::factory()->create();
    $shopInRoot->categories()->attach($root->id, ['principal' => true]);

    $shopInChild = Shop::factory()->create();
    $shopInChild->categories()->attach($child->id, ['principal' => true]);

    $shopUnrelated = Shop::factory()->create();

    $export = new CategoryShopsExport($root, ['company']);
    $collection = $export->collection();

    expect($collection->pluck('id'))
        ->toContain($shopInRoot->id)
        ->toContain($shopInChild->id)
        ->not->toContain($shopUnrelated->id);
});

it('maps only selected columns', function () {
    $category = Category::factory()->create();
    $shop = Shop::factory()->create(['company' => 'Test Co', 'city' => 'Marche']);
    $shop->categories()->attach($category->id, ['principal' => true]);

    $export = new CategoryShopsExport($category, ['company', 'city']);

    expect($export->headings())->toBe(['Société', 'Ville']);
    expect($export->map($shop))->toBe(['Test Co', 'Marche']);
});

it('downloads the export via the action', function () {
    Excel::fake();

    $category = Category::factory()->create();
    $shop = Shop::factory()->create();
    $shop->categories()->attach($category->id, ['principal' => true]);

    livewire(ViewCategory::class, ['record' => $category->id])
        ->callAction('exportXls', [
            'Business' => ['company'],
        ]);

    $expectedFilename = 'category-'.$category->slug.'-'.date('Y-m-d').'.xlsx';

    Excel::assertDownloaded($expectedFilename);
});
