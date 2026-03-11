<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Situation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Situation> */
final class SituationFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
        ];
    }
}
