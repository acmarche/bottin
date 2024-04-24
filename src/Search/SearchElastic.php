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
                            [
                                'multi_match' => [
                                    'query' => $keyword,
                                    'type' => 'best_fields',
                                    'operator' => 'OR',
                                    'fields' => [
                                        'societe^1.2',
                                        'societe.stemmed',
                                        'societe.edgengram',
                                        'email',
                                        'contact_email',
                                        'comment1',
                                        'comment1.stemmed',
                                        'secteurs',
                                        'secteurs.stemmed',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this->client->search($params);
    }
}
