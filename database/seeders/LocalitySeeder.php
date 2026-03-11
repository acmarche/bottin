<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Locality;
use Illuminate\Database\Seeder;

final class LocalitySeeder extends Seeder
{
    public function run(): void
    {
        Locality::factory(5)->create();
    }
}
