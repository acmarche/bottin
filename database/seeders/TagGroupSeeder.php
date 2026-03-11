<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TagGroup;
use Illuminate\Database\Seeder;

final class TagGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = ['Qualité', 'Service', 'Accessibilité', 'Promotion'];

        foreach ($groups as $group) {
            TagGroup::factory()->create(['name' => $group]);
        }
    }
}
