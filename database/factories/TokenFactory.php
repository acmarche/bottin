<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Shop;
use App\Models\Token;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Token> */
final class TokenFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'uuid' => fake()->uuid(),
            'password' => Str::random(50),
            'expire_at' => fake()->dateTimeBetween('now', '+1 year'),
        ];
    }
}
