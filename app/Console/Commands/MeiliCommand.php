<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Shop;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class MeiliCommand extends Command
{
    protected $signature = 'bottin:meili {--flush : Flush all indexes before importing}';

    protected $description = 'Import all searchable models into Scout (Meilisearch)';

    /**
     * @var array<int, class-string>
     */
    private array $searchableModels = [
        Shop::class,
        Category::class,
        Tag::class,
    ];

    public function handle(): int
    {
        foreach ($this->searchableModels as $model) {
            if ($this->option('flush')) {
                Artisan::call('scout:flush', ['model' => $model]);
            }

            Artisan::call('scout:import', ['model' => $model]);
        }

        return self::SUCCESS;
    }
}
