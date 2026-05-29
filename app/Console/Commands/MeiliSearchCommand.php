<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Shop;
use Illuminate\Console\Command;
use Meilisearch\Client;

final class MeiliSearchCommand extends Command
{
    protected $signature = 'bottin:search';

    protected $description = 'Import all searchable models into Scout (Meilisearch)';

    public function handle(): int
    {
        $this->info('Starting Scout import...');

        $localite = 'Marche-en-Famenne';
        $tags = [];
        $coordinates = null;

        $filters = [];

        if ($localite) {
            $filters[] = 'localite = "'.$localite.'"';
        }

        foreach ($tags as $tag) {
            $filters[] = 'tags = "'.$tag.'"';
        }

        if ($coordinates) {
            $distance = 5000;
            $filters[] = "_geoRadius({$coordinates['latitude']}, {$coordinates['longitude']}, {$distance})";
        }

        $index = app(Client::class)->index((new Shop)->searchableAs());
        $result = $index->search('', [
            'limit' => 500,
            'filter' => $filters,
        ]);

        foreach ($result->getRaw()['hits'] as $hit) {
            $this->line($hit['company'] ?? '');
        }

        $this->newLine();
        $this->info('All searchable models have been imported.');

        return self::SUCCESS;
    }
}
