<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Media;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Media> */
final class MediaFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'name' => fake()->name(),
            'is_main' => false,
            'file_name' => fake()->word().'.jpg',
            'mime_type' => 'image/jpeg',
        ];
    }
}
