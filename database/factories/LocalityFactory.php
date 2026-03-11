<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Locality;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Locality> */
final class LocalityFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->city(),
        ];
    }
}
