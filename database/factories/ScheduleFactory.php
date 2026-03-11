<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Schedule> */
final class ScheduleFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'day' => fake()->numberBetween(1, 7),
            'is_open_at_lunch' => false,
            'is_by_appointment' => false,
            'is_closed' => false,
            'morning_start' => '08:00',
            'morning_end' => '12:00',
            'noon_start' => '13:00',
            'noon_end' => '17:00',
        ];
    }
}
