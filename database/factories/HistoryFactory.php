<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\History;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<History> */
final class HistoryFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'made_by' => fake()->name(),
            'property' => fake()->word(),
            'old_value' => fake()->word(),
            'new_value' => fake()->word(),
        ];
    }
}
