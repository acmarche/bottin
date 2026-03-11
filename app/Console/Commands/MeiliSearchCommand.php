<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Shop;
use Illuminate\Console\Command;
use Meilisearch\Client;
use Meilisearch\Meilisearch;

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

        $meilisearch = new Meilisearch();
        $meilisearch->
        $index = app(Client::class)->index((new Shop)->searchableAs());
        dump($filters);
        $result = $index->search('', [
            'limit' => 500,
            'filter' => $filters,
        ]);

        foreach ($result->getRaw()['hits'] as $hit) {
            dd($hit);
            dump($hit['company']);
            dd($hit['tags']);
        }

        $this->newLine();
        $this->info('All searchable models have been imported.');

        return self::SUCCESS;
    }
}
