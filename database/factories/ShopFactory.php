<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Shop> */
final class ShopFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company' => fake()->company(),
            'street' => fake()->streetName(),
            'number' => fake()->buildingNumber(),
            'postal_code' => fake()->randomNumber(4, true),
            'city' => fake()->city(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'enabled' => fake()->boolean(),
            'city_center' => false,
            'open_at_lunch' => false,
            'pmr' => false,
            'click_collect' => false,
            'ecommerce' => false,
        ];
    }

    public function enabled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'enabled' => true,
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'enabled' => false,
        ]);
    }
}
