<?php

declare(strict_types=1);

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Categories\Pages\ViewCategory;
use App\Filament\Resources\Categories\RelationManagers\ChildrenRelationManager;
use App\Models\Category;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

it('can render the index page', function () {
    livewire(ListCategories::class)
        ->assertOk();
});

it('can render the create page', function () {
    livewire(CreateCategory::class)
        ->assertOk();
});

it('can render the edit page', function () {
    $category = Category::factory()->create();

    livewire(EditCategory::class, [
        'record' => $category->id,
    ])
        ->assertOk();
});

it('can create a category', function () {
    $category = Category::factory()->make();

    livewire(CreateCategory::class)
        ->fillForm([
            'name' => $category->name,
        ])
        ->call('create')
        ->assertNotified();

    assertDatabaseHas(Category::class, [
        'name' => $category->name,
    ]);
});

it('can update a category', function () {
    $category = Category::factory()->create();
    $newData = Category::factory()->make();

    livewire(EditCategory::class, [
        'record' => $category->id,
    ])
        ->fillForm([
            'name' => $newData->name,
        ])
        ->call('save')
        ->assertNotified();

    assertDatabaseHas(Category::class, [
        'id' => $category->id,
        'name' => $newData->name,
    ]);
});

it('can delete a category', function () {
    $category = Category::factory()->create();

    livewire(EditCategory::class, [
        'record' => $category->id,
    ])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseMissing($category);
});

it('can list root categories', function () {
    $root = Category::factory()->create(['parent_id' => null]);
    $child = Category::factory()->create(['parent_id' => $root->id]);

    livewire(ListCategories::class)
        ->loadTable()
        ->assertCanSeeTableRecords([$root])
        ->assertCanNotSeeTableRecords([$child]);
});

it('validates the form data', function (array $data, array $errors) {
    $category = Category::factory()->create();

    livewire(EditCategory::class, [
        'record' => $category->id,
    ])
        ->fillForm($data)
        ->call('save')
        ->assertHasFormErrors($errors)
        ->assertNotNotified();
})->with([
    '`name` is required' => [['name' => null], ['name' => 'required']],
    '`name` is max 255 characters' => [['name' => Str::random(256)], ['name' => 'max']],
]);

it('can render the view page', function () {
    $category = Category::factory()->create();

    livewire(ViewCategory::class, [
        'record' => $category->id,
    ])
        ->assertOk();
});

it('can load the children relation manager', function () {
    $parent = Category::factory()->create();
    $children = Category::factory()->count(3)->create(['parent_id' => $parent->id]);

    livewire(ChildrenRelationManager::class, [
        'ownerRecord' => $parent,
        'pageClass' => ViewCategory::class,
    ])
        ->assertOk()
        ->loadTable()
        ->assertCanSeeTableRecords($children);
});

it('shows breadcrumbs with ancestor chain', function () {
    $grandparent = Category::factory()->create(['parent_id' => null, 'name' => 'Grand-parent']);
    $parent = Category::factory()->create(['parent_id' => $grandparent->id, 'name' => 'Parent']);
    $child = Category::factory()->create(['parent_id' => $parent->id, 'name' => 'Enfant']);

    $component = livewire(ViewCategory::class, [
        'record' => $child->id,
    ]);

    $breadcrumbs = $component->instance()->getBreadcrumbs();

    expect($breadcrumbs)
        ->toHaveCount(4)
        ->and(array_values($breadcrumbs))
        ->toContain('Catégories', 'Grand-parent', 'Parent', 'Enfant')
        ->and(array_keys($breadcrumbs))
        ->toContain(
            CategoryResource::getUrl(),
            CategoryResource::getUrl('view', ['record' => $grandparent]),
            CategoryResource::getUrl('view', ['record' => $parent]),
        );
});
