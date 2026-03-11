<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Seeder;

final class ShopSeeder extends Seeder
{
    public function run(): void
    {
        Shop::factory(10)->create();
    }
}
