<?php

namespace AcMarche\Bottin\Search;

use AcMarche\Bottin\Elasticsearch\ElasticServer;
use AcMarche\Bottin\Entity\Fiche;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoDistanceQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Suggest\Suggest;

class SearchElastic implements SearchEngineInterface
{
    public Client $client;

    private ?Search $search = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws BadRequest400Exception
     */
    public function doSearchForCap(string $keyword): array
    {
        $this->getInstance();
        $boolQuery = $this->createQueryForFiche($keyword);

        $matchQuery = new MatchQuery('cap', 'true');
        $boolQuery->add($matchQuery, BoolQuery::FILTER);

        $this->search->addQuery($boolQuery);

        //  $this->addAggregations();

        $params = [
            'index' => ElasticServer::INDEX_NAME,
            'size' => 1_000,
            'body' => $this->search->toArray(),
        ];

        //   echo(json_encode($this->search->toArray()));

        return $this->client->search($params);
    }

    protected function createQueryForFiche(string $keyword): BoolQuery
    {
        $societeMatch = new MatchQuery(
            'societe',
            $keyword,
            [
                //  "cutoff_frequency" => 0.001, //TAVERNE LE PALACE
                'boost' => 1.2,
                //          "fuzziness" => "AUTO",//manda => mazda
            ]
        );

        $societeStemmedMatch = new MatchQuery(
            'societe.stemmed',
            $keyword,
            [
                'boost' => 1.1,
            ]
        );

        $societeNgramMatch = new MatchQuery(
            'societe.ngram',
            $keyword,
            [
            ]
        );

        $multiMatchQuery = new MultiMatchQuery(
            [
                'comment1',
                'comment1.stemmed',
                'secteurs',
                'secteurs.stemmed',
            ],
            $keyword
        );

        $ficheFilter = new MatchQuery('type', 'fiche');

        $boolQuery = new BoolQuery();
        $boolQuery->add($societeMatch, BoolQuery::SHOULD);
        $boolQuery->add($societeStemmedMatch, BoolQuery::SHOULD);
        $boolQuery->add($societeNgramMatch, BoolQuery::SHOULD);
        $boolQuery->add($multiMatchQuery, BoolQuery::SHOULD);
        $boolQuery->add($ficheFilter, BoolQuery::FILTER);

        $boolQuery->addParameter('minimum_should_match', 1);

        return $boolQuery;
    }

    /**
     * @throws BadRequest400Exception
     */
    public function doSearch(string $keyword, ?string $localite = null): array
    {
        $this->getInstance();
        $boolQuery = $this->createQueryForFiche($keyword);

        if ($localite) {
            $matchQuery = new MatchQuery('localite', $localite);
            $boolQuery->add($matchQuery, BoolQuery::FILTER);
        }

        $this->search->addQuery($boolQuery);

        //  $this->addAggregations();

        $params = [
            'index' => ElasticServer::INDEX_NAME,
            'size' => 100,
            'body' => $this->search->toArray(),
        ];

        //  var_dump($this->search->toArray());

        return $this->client->search($params);
    }

    /**
     * @throws BadRequest400Exception
     */
    public function doSearchAdvanced(string $keyword, ?string $localite = null): array
    {
        $this->getInstance();
        $boolQuery = $this->createQueryForFiche($keyword);

        if ($localite) {
            $matchQuery = new MatchQuery('localite', $localite);
            $boolQuery->add($matchQuery, BoolQuery::FILTER);
        }

        $this->search->addQuery($boolQuery);

        $this->addAggregations();
        $this->addSuggests($keyword);

        $params = [
            'index' => ElasticServer::INDEX_NAME,
            'size' => 100,
            'body' => $this->search->toArray(),
        ];

        //  var_dump($this->search->toArray());

        return $this->client->search($params);
    }

    protected function addAggregations(): void
    {
        $cap = new TermsAggregation('cap', 'cap.keyword');
        $localite = new TermsAggregation('localites', 'localite.keyword');
        $pmr = new TermsAggregation('pmr', 'pmr');
        $centreVille = new TermsAggregation('centre_ville', 'centreville');
        $midi = new TermsAggregation('midi', 'midi');
        $this->search->addAggregation($cap);
        $this->search->addAggregation($localite);
        $this->search->addAggregation($pmr);
        $this->search->addAggregation($centreVille);
        $this->search->addAggregation($midi);
    }

    protected function addSuggests(string $keyword): void
    {
        $suggest = new Suggest(
            'societe_suggest',
            'term',
            $keyword,
            'societe',
            ['size' => 5, 'suggest_mode' => 'popular']
        );
        $this->search->addSuggest($suggest);
    }

    protected function getAll(): Search
    {
        $search = new Search();
        $search->addQuery(new MatchAllQuery());

        return $search;
    }

    protected function location(string $latitude, string $longitude, string $distance): Search
    {
        $search = new Search();
        $boolQuery = new BoolQuery();
        $boolQuery->add(new MatchAllQuery());
        $geoDistanceQuery = new GeoDistanceQuery('location', $distance, ['lat' => $latitude, 'lon' => $longitude]);
        $boolQuery->add($geoDistanceQuery, BoolQuery::FILTER);
        $search->addQuery($boolQuery);

        return $search;
    }

    protected function laura(string $keyword): array
    {
        /**
         * search.
         */
        $latitude = 50.2268;
        $longitude = 5.3442;
        $query = [
            'bool' => [
                'must' => [
                    'multi_match' => [
                        'query' => $keyword,
                        'fuzziness' => 'AUTO',
                        'fields' => [
                            'societe',
                            'societe.stemmed',
                        ],
                    ],
                ],
                /*   "filter" => [
                       "geo_distance" => [
                           "distance" => "5km",
                           "location" => [5.3442, 50.2268]
                       ]
                   ]*/
            ],
        ];

        return $params = [
            'index' => 'bottin',
            'body' => [
                'profile' => 'true',
                'query' => $query,
                'aggs' => [
                    'centreville' => [
                        'terms' => [
                            'field' => 'centreville',
                        ],
                    ],
                    'localite' => [
                        'terms' => [
                            'field' => 'localite',
                        ],
                    ],
                    'pmr' => [
                        'terms' => [
                            'field' => 'pmr',
                        ],
                    ],
                    'midi' => [
                        'terms' => [
                            'field' => 'midi',
                        ],
                    ],
                ],
                'suggest' => [
                    'text' => $keyword,
                    'societe_suggest' => [
                        'phrase' => [
                            'field' => 'societe',
                            'size' => 1,
                            'gram_size' => 3,
                            'direct_generator' => [
                                [
                                    'field' => 'societe',
                                    'suggest_mode' => 'always',
                                    //"pre_filter" => "reverse",
                                    //"post_filter" => "reverse"
                                ],
                            ],
                            'highlight' => [
                                'pre_tag' => '<em>',
                                'post_tag' => '</em>',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getInstance(): void
    {
        $this->search = new Search();
    }

    public function renderResult(): array
    {
        // TODO: Implement renderResult() method.
    }

    /**
     * @return Fiche[]
     */
    public function getFiches(array $hits): array
    {
        $fiches = [];
        foreach ($hits['hits']['hits'] as $hit) {
            $fiches[] = $hit['_source'];
        }

        return $fiches;
    }
}
