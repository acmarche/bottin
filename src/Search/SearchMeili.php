<?php

namespace AcMarche\Bottin\Search;

use Meilisearch\Contracts\FacetSearchQuery;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class SearchMeili implements SearchEngineInterface
{
    use MeiliTrait;

    public function __construct(
        #[Autowire(env: 'BOTTIN_INDEX_NAME')]
        private string $indexName,
        #[Autowire(env: 'BOTTIN_INDEX_KEY')]
        private string $masterKey,
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

    public function doSearch(string $keyword, string $localite = null): iterable
    {
        $this->init();
        $index = $this->client->index($this->indexName);

        return $index->search($keyword, [
            'filter' => ['localite = aye'],
            'facets' => $this->facetFields,
        ]);

        return $this->client->index($this->indexName)->facetSearch(
            (new FacetSearchQuery())
                ->setFacetQuery($keyword)
                ->setFacetName('localite')
        //  ->setFilter(['rating > 3'])
        );

        //$facetSearch = new FacetSearchQuery();


    }

    public function doSearchAdvanced(string $keyword, string $localite = null): iterable
    {
        return [];
    }

    public function getFiches(iterable $hits): iterable
    {
        return [];
    }

}