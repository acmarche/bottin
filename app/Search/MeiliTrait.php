<?php

declare(strict_types=1);

namespace App\Search;

use Meilisearch\Client;
use Meilisearch\Endpoints\Indexes;

trait MeiliTrait
{
    public ?Client $client = null;

    public string $indexName;

    public string $masterKey;

    public ?Indexes $index = null;

    public string $primaryKey = 'id';

    private array $filterableAttributes = [
        'city',
        'tags',
        'type',
        '_geo',
    ];

    private array $sortableAttributes = [

    ];

    private array $facetFields = ['_geo', 'city', 'type', 'tags'];

    public function initClientAndIndex(): void
    {
        if (! $this->client) {
            $this->client = new Client('http://127.0.0.1:7700', $this->masterKey);
        }

        if (! $this->index) {
            $this->index = $this->client->index($this->indexName);
        }
    }
}
