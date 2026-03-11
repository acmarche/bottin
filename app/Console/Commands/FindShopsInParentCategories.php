<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Shop;
use Illuminate\Console\Command;

final class FindShopsInParentCategories extends Command
{
    protected $signature = 'bottin:find-shops-in-parent-categories';

    protected $description = 'Find shops classified in a category that has children';

    public function handle(): int
    {
        $shops = Shop::query()
            ->whereHas('categories', fn ($query) => $query->whereHas('children'))
            ->with(['categories' => fn ($query) => $query->whereHas('children')])
            ->get();

        if ($shops->isEmpty()) {
            $this->info('No shops found in parent categories.');

            return self::SUCCESS;
        }

        $this->warn("Found {$shops->count()} shop(s) classified in parent categories:");
        $this->newLine();

        $rows = [];
        foreach ($shops as $shop) {
            foreach ($shop->categories as $category) {
                $rows[] = [$shop->id, $shop->company, $category->id, $category->name];
            }
        }

        $this->table(
            ['Shop ID', 'Company', 'Category ID', 'Category'],
            $rows,
        );

        return self::SUCCESS;
    }
}
