<?php

namespace AcMarche\Bottin\Search;

use AcMarche\Bottin\Elasticsearch\ElasticClientTrait;
use AcMarche\Bottin\Elasticsearch\ElasticServer;
use AcMarche\Bottin\Entity\Fiche;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Elastica\Query\MultiMatch;
use Elastica\ResultSet;
use Elastica\Search;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Psr\Log\LoggerInterface;

class SearchElastic implements SearchEngineInterface
{
    use ElasticClientTrait;

    private ?BoolQuery $boolQuery = null;

    public function __construct(string $elasticIndexName, ?LoggerInterface $logger = null)
    {
        $this->connect($elasticIndexName);
        if (null !== $logger) {
            $this->client->setLogger($logger);
        }
    }

    public function doSearch(string $keyword, ?string $localite = null, int $limit = 50): iterable
    {
        $boolQuery = $this->createQueryForFiche($keyword, $localite);

        $query = new Query();
        $query->setQuery($boolQuery);

        $search = new Search($this->client);
        $search->addIndex($this->index);
        $search->setQuery($query);

        $options = ['limit' => $limit];

        return $search->search($query, $options);
    }

    private function createQueryForFiche(string $keyword, ?string $localite = null): BoolQuery
    {
        $this->boolQuery = new BoolQuery();

        if ($localite) {
            $match = new MatchQuery('localite', $localite);
            $this->boolQuery->addMust($match);
        }

        $match = new MultiMatch();
        $match->setFields(
            [
                'societe^1.2',
                'societe.stemmed',
                'societe.ngram',
                'comment1',
                'comment1.stemmed',
                'secteurs',
                'secteurs.stemmed',
            ]
        );
        $match->setQuery($keyword);
        $match->setType(MultiMatch::TYPE_MOST_FIELDS);
        $this->boolQuery->addMust($match);

        $ficheFilter = new MatchQuery('type', 'fiche');

        return $this->boolQuery;
    }

    public function doSearchForCap(string $keyword): iterable
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

        $search = new Search($this->client);

        return $search->search($query, $options);
    }

    /**
     * @throws BadRequest400Exception
     */
    public function doSearchAdvanced(string $keyword, ?string $localite = null): iterable
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
