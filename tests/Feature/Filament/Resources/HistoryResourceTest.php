<?php

declare(strict_types=1);

use App\Filament\Resources\Histories\Pages\ListHistories;
use App\Filament\Resources\Histories\Pages\ViewHistory;
use App\Models\History;
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
