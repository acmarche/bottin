<?php

declare(strict_types=1);

namespace App\Search;

use App\Models\Tag;
use Illuminate\Support\Facades\Log;
use Meilisearch\Search\SearchResult;
use stdClass;

final class SearchMeili
{
    use MeiliTrait;

    public function __construct()
    {
        $this->indexName = config('bottin.meili.index_name');
        $this->masterKey = config('bottin.meili.key');
    }

    public function doSearchMap(
        ?string $localite = null,
        array $tags = [],
        ?stdClass $coordinates = null
    ): iterable|SearchResult {
        $this->initClientAndIndex();
        $index = $this->client->index($this->indexName);
        $filters = ['type = fiche'];
        if ($localite) {
            $filters[] = 'city = '.$localite;
        }

        $tag = Tag::query()->where('id', 14)->first();
        $tags[] = $tag->name;

        foreach ($tags as $tag) {
            $filters[] = 'tags = "'.$tag.'"';
        }

        if ($coordinates) {
            $distance = 5000; // meters
            $filters[] = "_geoRadius($coordinates->latitude, $coordinates->longitude, $distance)";
        }

        Log::info('search', ['filters' => $filters]);

        return $index->search('', [
            'limit' => 500,
            'filter' => $filters,
            'facets' => $this->facetFields,
        ]);
    }
}
