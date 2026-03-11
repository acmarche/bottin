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
        $this->info('Starting Scout import...');

        foreach ($this->searchableModels as $model) {
            $modelName = class_basename($model);

            if ($this->option('flush')) {
                $this->components->task("Flushing {$modelName}", function () use ($model) {
                    Artisan::call('scout:flush', ['model' => $model]);
                });
            }

            $this->components->task("Importing {$modelName}", function () use ($model) {
                Artisan::call('scout:import', ['model' => $model]);
            });
        }

        $this->newLine();
        $this->info('All searchable models have been imported.');

        return self::SUCCESS;
    }
}
