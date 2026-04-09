<?php

declare(strict_types=1);

use App\Filament\Resources\Tags\Pages\CreateTag;
use App\Filament\Resources\Tags\Pages\EditTag;
use App\Filament\Resources\Tags\Pages\ListTags;
use App\Filament\Resources\Tags\Pages\ViewTag;
use App\Models\Shop;
use App\Models\Tag;
use App\Models\TagGroup;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

it('can render the index page', function () {
    livewire(ListTags::class)
        ->assertOk();
});

it('can render the create page', function () {
    livewire(CreateTag::class)
        ->assertOk();
});

it('can render the view page', function () {
    $tag = Tag::factory()->create();

    livewire(ViewTag::class, [
        'record' => $tag->id,
    ])
        ->assertOk();
});

it('can view a tag with linked shops', function () {
    $tag = Tag::factory()->create();
    $shops = Shop::factory()->count(3)->create();
    $tag->shops()->attach($shops);

    livewire(ViewTag::class, [
        'record' => $tag->id,
    ])
        ->assertOk();
});

it('can render the edit page', function () {
    $tag = Tag::factory()->create();

    livewire(EditTag::class, [
        'record' => $tag->id,
    ])
        ->assertOk();
});

it('can create a tag', function () {
    $tagGroup = TagGroup::factory()->create();
    $tag = Tag::factory()->make();

    livewire(CreateTag::class)
        ->fillForm([
            'name' => $tag->name,
            'tag_group_id' => $tagGroup->id,
            'private' => $tag->private,
        ])
        ->call('create')
        ->assertNotified();

    assertDatabaseHas(Tag::class, [
        'name' => $tag->name,
    ]);
});

it('can update a tag', function () {
    $tagGroup = TagGroup::factory()->create();
    $tag = Tag::factory()->create(['tag_group_id' => $tagGroup->id]);
    $newData = Tag::factory()->make();

    livewire(EditTag::class, [
        'record' => $tag->id,
    ])
        ->fillForm([
            'name' => $newData->name,
            'tag_group_id' => $tagGroup->id,
        ])
        ->call('save')
        ->assertNotified();

    assertDatabaseHas(Tag::class, [
        'id' => $tag->id,
        'name' => $newData->name,
    ]);
});

it('can delete a tag', function () {
    $tag = Tag::factory()->create();

    livewire(EditTag::class, [
        'record' => $tag->id,
    ])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseMissing($tag);
});

it('can validate unique name', function () {
    $existingTag = Tag::factory()->create();

    livewire(CreateTag::class)
        ->fillForm(['name' => $existingTag->name])
        ->call('create')
        ->assertHasFormErrors(['name' => ['unique']]);
});

it('can display shops count in the table', function () {
    $tag = Tag::factory()->create();
    $shops = Shop::factory()->count(3)->create();
    $tag->shops()->attach($shops);

    livewire(ListTags::class)
        ->assertCanSeeTableRecords([$tag])
        ->assertTableColumnStateSet('shops_count', 3, $tag);
});

it('validates the form data', function (array $data, array $errors) {
    $tag = Tag::factory()->create();

    livewire(EditTag::class, [
        'record' => $tag->id,
    ])
        ->fillForm($data)
        ->call('save')
        ->assertHasFormErrors($errors)
        ->assertNotNotified();
})->with([
    '`name` is required' => [['name' => null], ['name' => 'required']],
    '`name` is max 255 characters' => [['name' => Str::random(256)], ['name' => 'max']],
]);
