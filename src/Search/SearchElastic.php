<?php

namespace AcMarche\Bottin\Search;

use AcMarche\Bottin\Elasticsearch\ElasticClientTrait;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;

class SearchElastic
{
    use ElasticClientTrait;

    /**
     * @param string $keyword
     * @return Elasticsearch|Promise
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    function search(string $keyword): Elasticsearch|Promise
    {
        $this->connect();
        $params = [
            'index' => $this->indexName,
            'size' => 50,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['cap' => true]],
                            ['term' => ['type' => 'fiche']],
                        ],
                        'should' => [
                            ['match' => ['societe' => $keyword]],
                            ['match' => ['email' => $keyword]],
                        ],
                    ],
                ],
            ],
        ];

        return $this->client->search($params);
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
}
