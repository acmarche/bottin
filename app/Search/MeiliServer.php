<?php

declare(strict_types=1);

namespace App\Search;

use Meilisearch\Contracts\DeleteTasksQuery;
use Meilisearch\Endpoints\Keys;

final class MeiliServer
{
    use MeiliTrait;

    public function __construct()
    {
        $this->indexName = config('bottin.meili.index_name');
        $this->masterKey = config('bottin.meili.key');
    }

    public function addContent(): void
    {
        $data = new MeiliData();
        $data->addFiches();
        $data->addCategories();
    }

    public function createIndex(): void
    {
        $this->client->deleteTasks((new DeleteTasksQuery())->setStatuses(['failed', 'canceled', 'succeeded']));
        dump($this->client->deleteIndex($this->indexName));
        dump($this->client->createIndex($this->indexName, ['primaryKey' => $this->primaryKey]));
    }

    /**
     * https://raw.githubusercontent.com/meilisearch/meilisearch/latest/config.toml
     * curl -X PATCH 'http://localhost:7700/experimental-features/' -H 'Content-Type: application/json' -H 'Authorization: Bearer xxxxxx' --data-binary '{"containsFilter": true}'
     */
    public function settings(): void
    {
        $index = $this->client->index($this->indexName);

        $index->updateSearchableAttributes([
            'city',
            'tags',
            'type',
            '_geo',
        ]);

        $index->updateFilterableAttributes($this->filterableAttributes);
        $index->updateSortableAttributes($this->sortableAttributes);
    }

    public function createApiKey(): Keys
    {
        return $this->client->createKey([
            'description' => 'indicateur ville API key',
            'actions' => ['*'],
            'indexes' => [$this->indexName],
            'expiresAt' => '2042-04-02T00:42:42Z',
        ]);
    }

    public function dump(): array
    {
        return $this->client->createDump();
    }
}
