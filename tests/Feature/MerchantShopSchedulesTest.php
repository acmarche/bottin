<?php

declare(strict_types=1);

use App\Livewire\Merchant\ShopSchedulesTable;
use App\Models\Schedule;
use App\Models\Shop;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;

test('the schedules table lists the shop schedules', function (): void {
    $shop = Shop::factory()->create();
    $schedule = Schedule::factory()->create(['shop_id' => $shop->id, 'day' => 1]);
    $other = Schedule::factory()->create();

    livewire(ShopSchedulesTable::class, ['shopId' => $shop->id])
        ->loadTable()
        ->assertCanSeeTableRecords([$schedule])
        ->assertCanNotSeeTableRecords([$other]);
});

test('a merchant can create a schedule for their shop', function (): void {
    $shop = Shop::factory()->create();

    livewire(ShopSchedulesTable::class, ['shopId' => $shop->id])
        ->callAction(TestAction::make('create')->table(), [
            'day' => 2,
            'is_closed' => false,
            'is_by_appointment' => false,
            'morning_start' => '09:00',
            'morning_end' => '12:00',
            'noon_start' => '14:00',
            'noon_end' => '18:00',
        ]);

    $this->assertDatabaseHas(Schedule::class, [
        'shop_id' => $shop->id,
        'day' => 2,
    ]);
});

test('a merchant can edit a schedule for their shop', function (): void {
    $shop = Shop::factory()->create();
    $schedule = Schedule::factory()->create(['shop_id' => $shop->id, 'day' => 3]);

    livewire(ShopSchedulesTable::class, ['shopId' => $shop->id])
        ->callAction(TestAction::make('edit')->table($schedule), [
            'day' => 3,
            'is_closed' => true,
            'is_by_appointment' => false,
        ]);

    expect($schedule->refresh()->is_closed)->toBeTrue();
});

test('a day already used by the shop cannot be reused', function (): void {
    $shop = Shop::factory()->create();
    Schedule::factory()->create(['shop_id' => $shop->id, 'day' => 4]);

    livewire(ShopSchedulesTable::class, ['shopId' => $shop->id])
        ->callAction(TestAction::make('create')->table(), [
            'day' => 4,
            'is_closed' => false,
            'is_by_appointment' => false,
            'morning_start' => '09:00',
            'morning_end' => '12:00',
        ])
        ->assertHasActionErrors(['day']);
});
