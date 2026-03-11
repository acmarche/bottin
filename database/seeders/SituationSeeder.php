<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Situation;
use Illuminate\Database\Seeder;

final class SituationSeeder extends Seeder
{
    public function run(): void
    {
        Situation::factory(3)->create();
    }
}
