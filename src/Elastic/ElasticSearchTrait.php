<?php


namespace AcMarche\Bottin\Elastic;

use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoDistanceQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Suggest\Suggest;

trait ElasticSearchTrait
{
    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    /**
     * @var Search $search
     */
    private $search;

    /**
     * @param string $keyword
     * @return array
     * @throws BadRequest400Exception
     */
    function doSearchForCap(string $keyword): array
    {
        $this->getInstance();
        $query = $this->createQueryForFiche($keyword);

        $capFilter = new MatchQuery("cap", "true");
        $query->add($capFilter, BoolQuery::FILTER);

        $this->search->addQuery($query);

        //  $this->addAggregations();

        $params = [
            'index' => $this->indexName,
            'size' => 1000,
            'body' => $this->search->toArray(),
        ];

        //   echo(json_encode($this->search->toArray()));

        return $this->client->search($params);
    }

    /**
     * @param string $keyword
     * @return BoolQuery
     */
    protected function createQueryForFiche(string $keyword): BoolQuery
    {
        $societeMatch = new MatchQuery(
            'societe', $keyword,
            [
                //  "cutoff_frequency" => 0.001, //TAVERNE LE PALACE
                "boost" => 1.2,
      //          "fuzziness" => "AUTO",//manda => mazda
            ]
        );

        $societeStemmedMatch = new MatchQuery(
            'societe.stemmed', $keyword,
            [
                "boost" => 1.1
            ]
        );

        $societeNgramMatch = new MatchQuery(
            'societe.ngram', $keyword,
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

        $ficheFilter = new MatchQuery("type", "fiche");

        $query = new BoolQuery();
        $query->add($societeMatch, BoolQuery::SHOULD);
        $query->add($societeStemmedMatch, BoolQuery::SHOULD);
        $query->add($societeNgramMatch, BoolQuery::SHOULD);
        $query->add($multiMatchQuery, BoolQuery::SHOULD);
        $query->add($ficheFilter, BoolQuery::FILTER);

        $query->addParameter('minimum_should_match', 1);

        return $query;
    }


    /**
     * @param string $keyword
     * @return array
     * @throws BadRequest400Exception
     */
    function doSearch(string $keyword, ?string $localite): array
    {
        $this->getInstance();
        $query = $this->createQueryForFiche($keyword);

        if ($localite) {
            $localiteFilter = new MatchQuery('localite', $localite);
            $query->add($localiteFilter, BoolQuery::FILTER);
        }

        $this->search->addQuery($query);

        //  $this->addAggregations();

        $params = [
            'index' => $this->indexName,
            'size' => 100,
            'body' => $this->search->toArray(),
        ];

      //  var_dump($this->search->toArray());

        return $this->client->search($params);
    }

    protected function addAggregations()
    {
        $cap = new TermsAggregation('cap', 'cap.keyword');
        $localite = new TermsAggregation('localites', 'localite.keyword');
        $pmr = new TermsAggregation('pmr', 'pmr');
        $centreVille = new TermsAggregation('centre_ville', 'centre_ville');
        $midi = new TermsAggregation('midi', 'midi');
        $this->search->addAggregation($cap);
        $this->search->addAggregation($localite);
        $this->search->addAggregation($pmr);
        $this->search->addAggregation($centreVille);
        $this->search->addAggregation($midi);
    }

    protected function addSuggests(string $keyword)
    {
        $suggest = new Suggest(
            'societe_suggest', 'term', $keyword, 'societe', ['size' => 5, 'suggest_mode' => 'popular']
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
        $geoQuery = new GeoDistanceQuery('location', $distance, ['lat' => $latitude, 'lon' => $longitude]);
        $boolQuery->add($geoQuery, BoolQuery::FILTER);
        $search->addQuery($boolQuery);

        return $search;
    }

    protected function laura(string $keyword)
    {
        /**
         * search
         */
        $latitude = 50.2268;
        $longitude = 5.3442;
        $query = [
            "bool" => [
                "must" => [
                    "multi_match" => [
                        "query" => $keyword,
                        "fuzziness" => "AUTO",
                        "fields" => [
                            "societe",
                            "societe.stemmed",
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
                "aggs" => [
                    "centreville" => [
                        "terms" => [
                            "field" => "centreville",
                        ],
                    ],
                    "localite" => [
                        "terms" => [
                            "field" => "localite",
                        ],
                    ],
                    "pmr" => [
                        "terms" => [
                            "field" => "pmr",
                        ],
                    ],
                    "midi" => [
                        "terms" => [
                            "field" => "midi",
                        ],
                    ],
                ],
                "suggest" => [
                    "text" => $keyword,
                    "societe_suggest" => [
                        "phrase" => [
                            "field" => "societe",
                            "size" => 1,
                            "gram_size" => 3,
                            "direct_generator" => [
                                [
                                    "field" => "societe",
                                    "suggest_mode" => "always",
                                    //"pre_filter" => "reverse",
                                    //"post_filter" => "reverse"
                                ],
                            ],
                            "highlight" => [
                                "pre_tag" => "<em>",
                                "post_tag" => "</em>",
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getInstance()
    {
        $this->search = new Search();
    }
}
