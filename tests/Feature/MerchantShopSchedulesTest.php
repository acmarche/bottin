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
            'is_morning_by_appointment' => false,
            'is_noon_by_appointment' => false,
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
            'is_morning_by_appointment' => false,
            'is_noon_by_appointment' => false,
        ]);

    expect($schedule->refresh()->is_closed)->toBeTrue();
});

test('a schedule by appointment keeps its hours', function (): void {
    $shop = Shop::factory()->create();

    livewire(ShopSchedulesTable::class, ['shopId' => $shop->id])
        ->callAction(TestAction::make('create')->table(), [
            'day' => 5,
            'is_closed' => false,
            'is_morning_by_appointment' => true,
            'is_noon_by_appointment' => true,
            'morning_start' => '09:00',
            'morning_end' => '12:00',
        ]);

    $schedule = Schedule::query()->where('shop_id', $shop->id)->where('day', 5)->sole();

    expect($schedule->is_morning_by_appointment)->toBeTrue()
        ->and($schedule->is_noon_by_appointment)->toBeTrue()
        ->and(mb_substr((string) $schedule->morning_start, 0, 5))->toBe('09:00');
});

test('a schedule can be by appointment in the morning only', function (): void {
    $shop = Shop::factory()->create();

    livewire(ShopSchedulesTable::class, ['shopId' => $shop->id])
        ->callAction(TestAction::make('create')->table(), [
            'day' => 1,
            'is_closed' => false,
            'is_morning_by_appointment' => true,
            'is_noon_by_appointment' => false,
            'morning_start' => '09:00',
            'morning_end' => '12:30',
            'noon_start' => '13:30',
            'noon_end' => '17:30',
        ]);

    $schedule = Schedule::query()->where('shop_id', $shop->id)->where('day', 1)->sole();

    expect($schedule->is_morning_by_appointment)->toBeTrue()
        ->and($schedule->is_noon_by_appointment)->toBeFalse()
        ->and($schedule->isByAppointment())->toBeTrue();
});

test('a day already used by the shop cannot be reused', function (): void {
    $shop = Shop::factory()->create();
    Schedule::factory()->create(['shop_id' => $shop->id, 'day' => 4]);

    livewire(ShopSchedulesTable::class, ['shopId' => $shop->id])
        ->callAction(TestAction::make('create')->table(), [
            'day' => 4,
            'is_closed' => false,
            'is_morning_by_appointment' => false,
            'is_noon_by_appointment' => false,
            'morning_start' => '09:00',
            'morning_end' => '12:00',
        ])
        ->assertHasActionErrors(['day']);
});
