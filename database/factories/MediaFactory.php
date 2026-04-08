<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\MediaLibrary\MediaCollections\Models\Media as MediaSpatie;

/**
 * @extends Factory<MediaSpatie>
 */
final class MediaFactory extends Factory
{
    protected $model = MediaSpatie::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_type' => Shop::class,
            'model_id' => Shop::factory(),
            'collection_name' => 'images',
            'name' => fake()->word(),
            'file_name' => fake()->word().'.jpg',
            'mime_type' => 'image/jpeg',
            'disk' => 'public',
            'size' => 1024,
            'manipulations' => '[]',
            'custom_properties' => json_encode(['is_main' => false]),
            'generated_conversions' => '[]',
            'responsive_images' => '[]',
        ];
    }

    public function isMain(): static
    {
        return $this->state([
            'custom_properties' => json_encode(['is_main' => true]),
        ]);
    }
}
