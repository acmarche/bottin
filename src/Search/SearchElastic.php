<?php

namespace AcMarche\Bottin\Search;

use AcMarche\Bottin\Elasticsearch\ElasticClientTrait;
use AcMarche\Bottin\Entity\Fiche;
use Elastica\Aggregation\Terms as AggregationTerms;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\GeoDistance;
use Elastica\Query\MatchQuery;
use Elastica\Query\MultiMatch;
use Elastica\ResultSet;
use Elastica\Search;
use Elastica\Suggest;
use Elastica\Suggest\Term as SuggestTerm;
use Elasticsearch\ClientBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class SearchElastic implements SearchEngineInterface
{
    use ElasticClientTrait;

    private ?BoolQuery $boolQuery = null;

    public function __construct(
        #[Autowire(env: 'BOTTIN_INDEX_NAME')] string $elasticIndexName,
        LoggerInterface $logger = null
    ) {
        $this->connect($elasticIndexName);
        if ($logger instanceof LoggerInterface) {
            $this->client->setLogger($logger);
        }
    }

    public function doSearch(string $keyword, string $localite = null, int $limit = 50): iterable
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

    private function createQueryForFiche(string $keyword, string $localite = null): BoolQuery
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
                'societe.edgengram',
                'email',
                'contact_email',
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

    /**
     * @return iterable|ResultSet
     */
    public function doSearchForCap(string $keyword): array|callable
    {
        $boolQuery = $this->createQueryForFiche($keyword);

        $capFilter = new MatchQuery('cap', 'true');
        $this->boolQuery->addMust($capFilter);
        $ficheFilter = new MatchQuery('type', 'fiche');
        $this->boolQuery->addMust($ficheFilter);
        // $boolQuery->add($matchQuery, BoolQuery::FILTER);

        $query = new Query();
        $query->setQuery($boolQuery);

        $params = [
            'index' => $this->index->name,
            'body' => $query->toArray(),
        ];
        $client = ClientBuilder::create()->build();

        return $client->search($params);
    }

    public function doSearchAdvanced(string $keyword, string $localite = null): iterable
    {
        $boolQuery = $this->createQueryForFiche($keyword);

        if ($localite) {
            $match = new MatchQuery('localite', $localite);
            $this->boolQuery->addMust($match);
        }

        $query = new Query();
        $query->setQuery($boolQuery);

        $this->addAggregations($query);
        $query->setSuggest($this->addSuggests($keyword));

        $search = new Search($this->client);
        $search->addIndex($this->index);
        $search->setQuery($query);

        $options = ['limit' => 100];

        return $search->search($query, $options);
    }

    protected function addAggregations(Query $query): void
    {
        $cap = new AggregationTerms('cap');
        $cap->setField('cap.keyword');

        $localite = new AggregationTerms('localites');
        $localite->setField('localite.keyword');

        $pmr = new AggregationTerms('pmr');
        $pmr->setField('pmr');

        $centreVille = new AggregationTerms('centre_ville');
        $centreVille->setField('centreville');

        $midi = new AggregationTerms('midi');
        $midi->setField('midi');

        $query->addAggregation($cap);
        $query->addAggregation($localite);
        $query->addAggregation($pmr);
        $query->addAggregation($centreVille);
        $query->addAggregation($midi);
    }

    protected function addSuggests(string $keyword): Suggest
    {
        $suggestSociete = new SuggestTerm('societe_suggest', 'societe');
        $suggestSociete->setSize(5);
        $suggestSociete->setSuggestMode('popular');

        $suggest = new Suggest();
        $suggest->addSuggestion($suggestSociete->setText($keyword));

        return $suggest;
        // $query->addSuggest($suggest);
    }

    /**
     * @return Fiche[]
     */
    public function getFiches(iterable $hits): iterable
    {
        $fiches = [];
        foreach ($hits['hits']['hits'] as $hit) {
            $fiches[] = $hit['_source'];
        }

        return $fiches;
    }

    protected function location(string $latitude, string $longitude, string $distance): GeoDistance
    {
        $geoQuery = new GeoDistance('point', ['lat' => $longitude, 'lon' => $longitude], $distance);

        $query = new Query();
        $query->setPostFilter($geoQuery);

        return $geoQuery;
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
                                    // "pre_filter" => "reverse",
                                    // "post_filter" => "reverse"
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
}
