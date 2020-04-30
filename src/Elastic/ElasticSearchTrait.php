<?php


namespace AcMarche\Bottin\Elastic;

use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchPhraseQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoDistanceQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
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
        /*$boolQuery = new BoolQuery();
$boolQuery->add(new MatchQuery('societe', $keyword));
$geoQuery = new TermQuery('localite', $localite);
$boolQuery->add($geoQuery, BoolQuery::FILTER);
$this->search->addQuery($boolQuery);*/

        $societeMatch = new MultiMatchQuery(
            ['societe'],
            $keyword,
            [
                //  "cutoff_frequency" => 0.001, //TAVERNE LE PALACE
                "boost" => 1.2
                /*   "minimum_should_match" => [
                       "low_freq" => 2,
                       "high_freq" => 3,
                   ],*/
            ]
        );
        $societeMatch = new MatchQuery(
            'fiche.societe',
            $keyword,
            [
                //  "cutoff_frequency" => 0.001, //TAVERNE LE PALACE
                "boost" => 1.2
                /*   "minimum_should_match" => [
                       "low_freq" => 2,
                       "high_freq" => 3,
                   ],*/
            ]
        );

        $comment1Match = new MatchQuery(
            "comment1",
            $keyword,
            [

            ]
        );

        $capFilter = new MatchQuery(
            "cap",
            true,
            [

            ]
        );

        $query = new BoolQuery();
        $multiMatchQuery = new MultiMatchQuery(
            ['fiche.societe', 'fiche.societe.stemmed', 'fiche.comment1', 'fiche.secteurs'],
            $keyword
        );
        //    $query->add($comment1Match, BoolQuery::SHOULD);
        //  $query->add($societeMatch, BoolQuery::SHOULD);

        $this->search->addQuery($multiMatchQuery);

        //   $query->add($capFilter, BoolQuery::FILTER);

        //  $this->addAggregations();

        $params = [
            'index' => $this->indexName,
            'size' => 1000,
        ];

        //var_dump($this->search->toArray());
        $params['body'] = $this->search->toArray();

        var_dump($params);

        return $this->client->search($params);
    }


    /**
     * @param string $keyword
     * @return array
     * @throws BadRequest400Exception
     */
    function doSearch(string $keyword, ?string $localite, string $type): array
    {
        $this->getInstance();
        switch ($type) {
            case 'match':
                $this->match($keyword, $localite);
                //    $laura = $this->laura($keyword);
                break;
            case 'multiMatch':
                $this->multiMatch($keyword);
                break;
            case 'matchPhrase':
                $this->matchPhrase($keyword);
                break;
            case 'all':
                $this->getAll();
                break;
            case 'filter':
                $this->filter();
                break;
        }

        $params = [
            'index' => $this->indexName,
            'size' => 100,
        ];
        //   $this->addAggregations();
        //   $this->addSuggests($keyword);
        //   dump($this->search->getQueries());
        $params['body'] = $this->search->toArray();

        //   dump($params);

        return $this->client->search($params);
    }

    protected function match(string $keyword, ?string $localite)
    {
        /*$boolQuery = new BoolQuery();
        $boolQuery->add(new MatchQuery('societe', $keyword));
        $geoQuery = new TermQuery('localite', $localite);
        $boolQuery->add($geoQuery, BoolQuery::FILTER);
        $this->search->addQuery($boolQuery);*/

        $societeMatch = new MatchQuery(
            "societe",
            $keyword,
            [
                //  "cutoff_frequency" => 0.001, //TAVERNE LE PALACE
                "boost" => 1.2
                /*   "minimum_should_match" => [
                       "low_freq" => 2,
                       "high_freq" => 3,
                   ],*/
            ]
        );

        $comment1Match = new MatchQuery(
            "comment1",
            $keyword,
            [

            ]
        );

        $multiMatchQuery = new MultiMatchQuery(
            ['societe', 'comment1'],
            $keyword
        );

        $query = new BoolQuery();
        $query->add($comment1Match, BoolQuery::SHOULD);
        //$query->add($multiMatchQuery, BoolQuery::SHOULD);
        //$query->addParameter('analyzer', 'french_heavy');

        if ($localite) {
            $localiteFilter = new MatchQuery('localite', $localite);
            $query->add($localiteFilter, BoolQuery::FILTER);
            $query->add($societeMatch, BoolQuery::MUST);
        } else {
            $query->add($societeMatch, BoolQuery::SHOULD);
        }

        $this->search->addQuery($query);
    }

    protected function multiMatch(string $keyword): Search
    {
        $search = new Search();
        $termQueryForTag1 = new TermQuery("societe", $keyword);
        $termQueryForTag2 = new TermQuery("societe.stemmed", $keyword);
        $termQueryForTag3 = new TermQuery("comment1", $keyword);

        $search->addQuery($termQueryForTag1);
        $search->addQuery($termQueryForTag2);
        $search->addQuery($termQueryForTag3, BoolQuery::SHOULD);

        return $search;
    }

    protected function matchPhrase(string $keyword): Search
    {
        $query = new MatchPhraseQuery('societe', $keyword);
        $query->addParameter('analyzer', 'french_light');

        $search = new Search();
        $search->addQuery($query);

        return $search;
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

    protected function filter(): Search
    {
        $search = new Search();
        $boolQuery = new BoolQuery();
        $boolQuery->add(new MatchAllQuery());
        $geoQuery = new TermQuery('localite', 'Aye');
        $boolQuery->add($geoQuery, BoolQuery::FILTER);
        $search->addQuery($boolQuery);

        return $search;
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
