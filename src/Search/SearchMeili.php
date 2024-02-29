<?php

namespace AcMarche\Bottin\Search;

use Meilisearch\Contracts\FacetSearchQuery;
use Meilisearch\Search\SearchResult;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class SearchMeili implements SearchEngineInterface
{
    use MeiliTrait;

    public function __construct(
        #[Autowire(env: 'MEILI_INDEX_NAME')]
        private string $indexName,
        #[Autowire(env: 'MEILI_MASTER_KEY')]
        private string $masterKey,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * https://www.meilisearch.com/docs/learn/fine_tuning_results/geosearch
     * @param float $latitude
     * @param float $longitude
     * @param int $distance in meters
     * @return void
     */
    public function searchGeo(float $latitude, float $longitude, int $distance = 200)
    {
        $this->client
            ->index($this->indexName)
            ->search('', [
                //'filter' => '_geoBoundingBox([45.494181, 9.214024], [45.449484, 9.179175])',
                //'filter' => "_geoRadius($latitude,$longitude, $distance) AND type = pizza",
                'filter' => "_geoRadius($latitude,$longitude, $distance)",
            ]);
    }

    /**
     * https://www.meilisearch.com/docs/learn/fine_tuning_results/filtering
     * @param string $keyword
     * @param string|null $localite
     * @return iterable|SearchResult
     */
    public function doSearch(string $keyword, string $localite = null): iterable|SearchResult
    {
        $this->init();
        $index = $this->client->index($this->indexName);
        $filters = ['filter' => ['type = fiche']];
        if ($localite) {
            $filters['filter'] = ['localite = '.$localite];
        }

        return $index->search($keyword, $filters);
    }

    /**
     * L'exemple de code suivant recherche dans la localite facette les valeurs $keyword :
     */
    public function doSearchFacet(string $keyword, string $localite = null): iterable
    {
        return $this->client->index($this->indexName)->facetSearch(
            (new FacetSearchQuery())
                ->setFacetQuery($keyword)
                ->setFacetName('localite')
        //  ->setFilter(['rating > 3'])
        );
    }

    public function doSearchAdvanced(
        string $keyword,
        ?string $localite = null,
        array $filters = []
    ): iterable|SearchResult {
        $this->init();
        $index = $this->client->index($this->indexName);
        $filter = ['type = fiche'];
        if ($localite) {
            $filter[] = 'localite = '.$localite;
        }

        if (count($filters) > 0) {
            foreach ($filters as $tag) {
                $filter[] = 'tags = "'.$tag.'"';
            }
        }

        //$args['attributesToHighlight'] = ['*'];
        //$args['showRankingScore'] = true;

        return $index->search($keyword, [
            //'filter' => [['tags = "Temps de Midi"', 'tags = Pmr'], 'localite = Marche-en-Famenne'], => OR
            //'filter' => ['type = fiche', 'tags = "Temps de Midi"', 'tags = Pmr', 'localite = Marche-en-Famenne'],// => AND
            'filter' => $filter,
            'facets' => $this->facetFields,
        ]);
    }

    public function doSearchMap(
        ?string $localite = null,
        array $filters = []
    ): iterable|SearchResult {
        $this->init();
        $index = $this->client->index($this->indexName);
        $filter = ['type = fiche'];
        if ($localite) {
            $filter[] = 'localite = '.$localite;
        }

        if (count($filters) > 0) {
            foreach ($filters as $tag) {
                $filter[] = 'tags = "'.$tag.'"';
            }
        }

        $this->logger->notice('MEILI: '.join(',', $filter).' END');

        return $index->search('', [
            'filter' => $filter,
            'facets' => $this->facetFields,
        ]);
    }

    public function doSearchForCap(string $keyword): array|SearchResult
    {
        $this->init();
        $index = $this->client->index($this->indexName);
        $filters = ['filter' => ['type = fiche' and 'cap = true']];

        return $index->search($keyword, $filters);
    }

}