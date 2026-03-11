<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PointOfSale;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PointOfSale> */
final class PointOfSaleFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
        ];
    }
}
