<?php

declare(strict_types=1);

use App\Exports\SelectedCategoriesShopsExport;
use App\Filament\Pages\ExportShops;
use App\Models\Category;
use App\Models\Selection;
use App\Models\Shop;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;

use function Pest\Livewire\livewire;

/** @return array{root: Category, first: Category, second: Category, grandchild: Category} */
function categoryBranch(): array
{
    $root = Category::factory()->create(['name' => 'Commerces']);
    $first = Category::factory()->create(['name' => 'Alimentation', 'parent_id' => $root->id]);
    $second = Category::factory()->create(['name' => 'Vêtements', 'parent_id' => $root->id]);
    $grandchild = Category::factory()->create(['name' => 'Boulangerie', 'parent_id' => $first->id]);

    return compact('root', 'first', 'second', 'grandchild');
}

function shopIn(Category $category): Shop
{
    $shop = Shop::factory()->create();
    $shop->categories()->attach($category->id, ['principal' => true]);

    return $shop;
}

it('displays the whole category tree', function () {
    categoryBranch();

    livewire(ExportShops::class)
        ->assertOk()
        ->assertSeeInOrder(['Commerces', 'Alimentation', 'Boulangerie', 'Vêtements']);
});

it('selects all descendants when checking a category', function () {
    ['root' => $root, 'first' => $first, 'second' => $second, 'grandchild' => $grandchild] = categoryBranch();

    $selected = livewire(ExportShops::class)
        ->call('toggleCategory', $root->id)
        ->get('selectedCategoryIds');

    expect($selected)->toEqualCanonicalizing([$root->id, $first->id, $second->id, $grandchild->id]);
});

it('deselects all descendants when unchecking a fully selected category', function () {
    ['root' => $root] = categoryBranch();

    livewire(ExportShops::class)
        ->call('toggleCategory', $root->id)
        ->call('toggleCategory', $root->id)
        ->assertSet('selectedCategoryIds', []);
});

it('keeps the other children when unchecking a single child', function () {
    ['root' => $root, 'first' => $first, 'second' => $second, 'grandchild' => $grandchild] = categoryBranch();

    $selected = livewire(ExportShops::class)
        ->call('toggleCategory', $root->id)
        ->call('toggleCategory', $second->id)
        ->get('selectedCategoryIds');

    expect($selected)->toEqualCanonicalizing([$root->id, $first->id, $grandchild->id]);
});

it('selects the whole subtree when checking a partially selected category', function () {
    ['root' => $root, 'first' => $first, 'second' => $second, 'grandchild' => $grandchild] = categoryBranch();

    $selected = livewire(ExportShops::class)
        ->call('toggleCategory', $second->id)
        ->call('toggleCategory', $root->id)
        ->get('selectedCategoryIds');

    expect($selected)->toEqualCanonicalizing([$root->id, $first->id, $second->id, $grandchild->id]);
});

it('marks a parent as partial when only some children are selected', function () {
    ['root' => $root, 'second' => $second] = categoryBranch();

    livewire(ExportShops::class)
        ->call('toggleCategory', $second->id)
        ->assertSeeHtml('category-checkbox-'.$root->id.'-partial')
        ->assertSeeHtml('category-checkbox-'.$second->id.'-checked');
});

it('rejects selection changes coming from the client', function () {
    $category = Category::factory()->create();

    livewire(ExportShops::class)->set('selectedCategoryIds', [$category->id]);
})->throws(CannotUpdateLockedPropertyException::class);

it('counts each selected shop once', function () {
    ['first' => $first, 'second' => $second] = categoryBranch();
    $shop = shopIn($first);
    $shop->categories()->attach($second->id, ['principal' => false]);
    shopIn(Category::factory()->create());

    expect(new SelectedCategoriesShopsExport([$first->id, $second->id])->count())->toBe(1);
});

it('exports only the shops of the selected categories', function () {
    ['root' => $root, 'first' => $first, 'second' => $second, 'grandchild' => $grandchild] = categoryBranch();
    $inRoot = shopIn($root);
    $inGrandchild = shopIn($grandchild);
    $inSecond = shopIn($second);

    $export = new SelectedCategoriesShopsExport([$root->id, $first->id, $grandchild->id], ['company']);

    expect($export->collection()->pluck('id'))
        ->toContain($inRoot->id, $inGrandchild->id)
        ->not->toContain($inSecond->id);
});

it('disables the export while nothing is selected', function () {
    Category::factory()->create();

    livewire(ExportShops::class)->assertActionDisabled('export');
});

it('downloads the selected shops', function () {
    ['second' => $second] = categoryBranch();
    shopIn($second);

    livewire(ExportShops::class)
        ->call('toggleCategory', $second->id)
        ->callAction('export', ['Business' => ['company']])
        ->assertHasNoErrors()
        ->assertFileDownloaded('fiches-categories-'.date('Y-m-d').'.xlsx');
});

it('refuses to export without any column', function () {
    ['second' => $second] = categoryBranch();

    livewire(ExportShops::class)
        ->call('toggleCategory', $second->id)
        ->callAction('export', ['Business' => [], 'Catégories' => []])
        ->assertNotified('Sélectionnez au moins une colonne')
        ->assertNoFileDownloaded();
});

it('lists only the selected categories of a shop, with their full path', function () {
    ['root' => $root, 'grandchild' => $grandchild, 'second' => $second] = categoryBranch();
    $shop = shopIn($grandchild);
    $shop->categories()->attach($second->id, ['principal' => false]);
    $shop->categories()->attach(Category::factory()->create(['name' => 'Non sélectionnée'])->id, ['principal' => false]);

    $export = new SelectedCategoriesShopsExport(
        [$grandchild->id, $second->id],
        ['company', SelectedCategoriesShopsExport::CATEGORIES_COLUMN],
    );

    expect($export->headings())->toBe(['Société', 'Catégories']);
    expect($export->map($export->collection()->first()))->toBe([
        $shop->company,
        'Commerces > Alimentation > Boulangerie; Commerces > Vêtements',
    ]);
});

it('ticks the categories column by default', function () {
    ['second' => $second] = categoryBranch();

    livewire(ExportShops::class)
        ->call('toggleCategory', $second->id)
        ->mountAction('export')
        ->assertSchemaStateSet([
            'Catégories' => [SelectedCategoriesShopsExport::CATEGORIES_COLUMN],
        ]);
});

it('restores the saved selection on a later visit', function () {
    ['first' => $first, 'grandchild' => $grandchild] = categoryBranch();

    livewire(ExportShops::class)->call('toggleCategory', $first->id);

    $selected = livewire(ExportShops::class)->get('selectedCategoryIds');

    expect($selected)->toEqualCanonicalizing([$first->id, $grandchild->id]);
});

it('saves the unticked categories and an emptied selection', function () {
    ['root' => $root, 'second' => $second] = categoryBranch();

    $page = livewire(ExportShops::class)
        ->call('toggleCategory', $root->id)
        ->call('toggleCategory', $second->id);

    expect(Selection::query()->pluck('category_id'))->toHaveCount(3)->not->toContain($second->id);

    $page->call('clearSelection');

    expect(Selection::query()->count())->toBe(0);
});

it('keeps each user selection separate', function () {
    ['second' => $second] = categoryBranch();
    $other = Category::factory()->create();
    Selection::query()->create(['category_id' => $other->id, 'user' => 'someone-else']);

    livewire(ExportShops::class)
        ->assertSet('selectedCategoryIds', [])
        ->call('toggleCategory', $second->id)
        ->call('clearSelection');

    expect(Selection::query()->where('user', 'someone-else')->count())->toBe(1);
});

it('ignores saved rows whose category was deleted', function () {
    Selection::query()->create(['category_id' => null, 'user' => auth()->user()->username]);

    livewire(ExportShops::class)->assertSet('selectedCategoryIds', []);
});
