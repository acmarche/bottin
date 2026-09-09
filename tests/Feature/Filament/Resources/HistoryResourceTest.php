<?php

declare(strict_types=1);

use App\Enums\HistoryActionEnum;
use App\Filament\Resources\Histories\Pages\ListHistories;
use App\Filament\Resources\Histories\Pages\ViewHistory;
use App\Models\History;
use App\Models\Shop;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;

it('can render the list page', function () {
    $histories = History::factory()->count(3)->create();

    livewire(ListHistories::class)
        ->assertOk()
        ->loadTable()
        ->assertCanSeeTableRecords($histories);
});

it('can render the view page', function () {
    $history = History::factory()->create();

    livewire(ViewHistory::class, ['record' => $history->id])
        ->assertOk();
});

it('can delete a history record', function () {
    $history = History::factory()->create();

    livewire(ListHistories::class)
        ->loadTable()
        ->callTableAction(DeleteAction::class, $history)
        ->assertNotified();

    assertDatabaseMissing($history);
});

it('can filter the list page by action', function () {
    $created = History::factory()->create([
        'property' => History::LIFECYCLE_PROPERTY,
        'old_value' => null,
        'new_value' => 'Acme Corp',
    ]);
    $deleted = History::factory()->create([
        'shop_id' => null,
        'property' => History::LIFECYCLE_PROPERTY,
        'old_value' => 'Gone Ltd',
        'new_value' => null,
    ]);
    $updated = History::factory()->create(['property' => 'email']);

    livewire(ListHistories::class)
        ->loadTable()
        ->filterTable('action', HistoryActionEnum::Created->value)
        ->assertCanSeeTableRecords([$created])
        ->assertCanNotSeeTableRecords([$deleted, $updated])
        ->filterTable('action', HistoryActionEnum::Deleted->value)
        ->assertCanSeeTableRecords([$deleted])
        ->assertCanNotSeeTableRecords([$created, $updated])
        ->filterTable('action', HistoryActionEnum::Updated->value)
        ->assertCanSeeTableRecords([$updated])
        ->assertCanNotSeeTableRecords([$created, $deleted]);
});

it('shows the name of a deleted shop and finds it when searching', function () {
    $shop = Shop::factory()->create(['company' => 'Wilson Optique']);
    $shop->delete();

    $deletion = History::query()
        ->where('property', History::LIFECYCLE_PROPERTY)
        ->whereNull('new_value')
        ->sole();

    expect($deletion->shopName())->toBe('Wilson Optique')
        ->and($deletion->action())->toBe(HistoryActionEnum::Deleted);

    livewire(ListHistories::class)
        ->loadTable()
        ->searchTable('Wilson')
        ->assertCanSeeTableRecords([$deletion]);
});
