<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Address> */
final class AddressFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'street' => fake()->streetName(),
            'number' => fake()->buildingNumber(),
            'postal_code' => fake()->randomNumber(4, true),
            'city' => fake()->city(),
        ];
    }
}
