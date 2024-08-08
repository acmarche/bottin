<?php

namespace AcMarche\Bottin\Elasticsearch;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;

/**
 * https://elasticsearch-cheatsheet.jolicode.com/
 * Class ElasticServer.
 */
class ElasticServer
{
    use ElasticClientTrait;

    private array $skips = [705];//705 shop and go

    /**
     * @return Elasticsearch|Promise
     * @throws ServerResponseException|AuthenticationException|ClientResponseException|MissingParameterException
     */
    public function reset(): Elasticsearch|Promise
    {
        $this->connect();
        try {
            $this->deleteIndex();
        } catch (\Exception $exception) {

        }

        return $this->createIndex();
    }

    /**
     * @return Elasticsearch|Promise
     * @throws ServerResponseException|AuthenticationException|ClientResponseException|MissingParameterException
     */
    public function createIndex(): Elasticsearch|Promise
    {
        $this->connect();
        $params = [
            'index' => $this->indexName,
            'body' => [
                "settings" => [
                    "index" => [
                        "number_of_shards" => 2,
                        "number_of_replicas" => 1,
                    ],
                    'analysis' => [
                        'filter' => [
                            'app_french_stemmer' => [
                                'type' => 'stemmer',
                                'language' => 'light_french',
                            ],
                            'app_french_elision' => [
                                'type' => 'elision',
                                'articles_case' => true,
                                'articles' => [
                                    'l',
                                    'm',
                                    't',
                                    'qu',
                                    'n',
                                    's',
                                    'j',
                                    'd',
                                    'c',
                                    'jusqu',
                                    'quoiqu',
                                    'lorsqu',
                                    'puisqu',
                                ],
                            ],
                            'app_autocomplete_filter' => [
                                'type' => 'edge_ngram',
                                'min_gram' => 1,
                                'max_gram' => 20,
                            ],
                        ],
                        'analyzer' => [
                            'app_french_heavy' => [
                                'tokenizer' => 'icu_tokenizer',
                                'filter' => [
                                    'app_french_elision',
                                    'icu_folding',
                                    'app_french_stemmer',
                                ],
                            ],
                            'app_french_light' => [
                                'tokenizer' => 'icu_tokenizer',
                                'filter' => [
                                    'app_french_elision',
                                    'icu_folding',
                                ],
                            ],
                            'app_autocomplete' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => [
                                    'lowercase',
                                    'asciifolding',
                                    'elision',
                                    'app_autocomplete_filter',
                                ],
                            ],
                            'keyword_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'keyword',
                                'filter' => [
                                    'lowercase',
                                    'asciifolding',
                                    'trim',
                                ],
                                'char_filter' => [],
                            ],
                            'edge_ngram_analyzer' => [
                                'tokenizer' => 'edge_ngram_tokenizer',
                                'filter' => [
                                    'lowercase',
                                ],
                            ],
                            'edge_ngram_search_analyzer' => [
                                'tokenizer' => 'lowercase',
                            ],
                        ],
                        'tokenizer' => [
                            'edge_ngram_tokenizer' => [
                                'type' => 'edge_ngram',
                                'min_gram' => 2,
                                'max_gram' => 5,
                                'token_chars' => [
                                    'letter',
                                ],
                            ],
                        ],
                    ],
                ],
                'mappings' => [
                    'dynamic' => false,
                    '_source' => [
                        'enabled' => true,
                    ],
                    'properties' => [
                        'id' => [
                            'type' => 'long',
                            'index' => true,
                        ],
                        'location' => [
                            'type' => 'geo_point',
                        ],
                        'cap' => [
                            'type' => 'keyword',
                        ],
                        'type' => [
                            'type' => 'keyword',
                        ],
                        'centre_ville' => [
                            'type' => 'keyword',
                        ],
                        'pmr' => [
                            'type' => 'keyword',
                        ],
                        'midi' => [
                            'type' => 'keyword',
                        ],
                        'localite' => [
                            'type' => 'text',
                            'analyzer' => 'app_french_light',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword',
                                ],
                            ],
                        ],
                        'societe' => [
                            'type' => 'text',
                            'analyzer' => 'app_french_light',
                            'fields' => [
                                'stemmed' => [
                                    'type' => 'text',
                                    'analyzer' => 'app_french_heavy',
                                ],
                                'ngram' => [
                                    'type' => 'text',
                                    'analyzer' => 'edge_ngram_analyzer',
                                ],
                                'edgengram' => [
                                    'type' => 'text',
                                    'analyzer' => 'edge_ngram_analyzer',
                                    'search_analyzer' => 'edge_ngram_search_analyzer',
                                ],
                            ],
                        ],
                        'description' => [
                            'type' => 'text',
                            'analyzer' => 'app_french_light',
                            'fields' => [
                                'stemmed' => [
                                    'type' => 'text',
                                    'analyzer' => 'app_french_heavy',
                                ],
                                'ngram' => [
                                    'type' => 'text',
                                    'analyzer' => 'edge_ngram_analyzer',
                                ],
                            ],
                        ],
                        'nom' => [
                            'type' => 'text',
                            'analyzer' => 'app_french_light',
                            'fields' => [
                                'stemmed' => [
                                    'type' => 'text',
                                    'analyzer' => 'app_french_heavy',
                                ],
                                'ngram' => [
                                    'type' => 'text',
                                    'analyzer' => 'edge_ngram_analyzer',
                                ],
                            ],
                        ],
                        'fonction' => [
                            'type' => 'text',
                            'analyzer' => 'app_french_light',
                            'fields' => [
                                'stemmed' => [
                                    'type' => 'text',
                                    'analyzer' => 'app_french_heavy',
                                ],
                                'ngram' => [
                                    'type' => 'text',
                                    'analyzer' => 'edge_ngram_analyzer',
                                ],
                            ],
                        ],
                        'comment1' => [
                            'type' => 'text',
                            'analyzer' => 'app_french_light',
                            'fields' => [
                                'stemmed' => [
                                    'type' => 'text',
                                    'analyzer' => 'app_french_heavy',
                                ],
                                'ngram' => [
                                    'type' => 'text',
                                    'analyzer' => 'edge_ngram_analyzer',
                                ],
                            ],
                        ],
                        'comment2' => [
                            'type' => 'text',
                            'analyzer' => 'app_french_light',
                            'fields' => [
                                'stemmed' => [
                                    'type' => 'text',
                                    'analyzer' => 'app_french_heavy',
                                ],
                                'ngram' => [
                                    'type' => 'text',
                                    'analyzer' => 'edge_ngram_analyzer',
                                ],
                            ],
                        ],
                        'comment3' => [
                            'type' => 'text',
                            'analyzer' => 'app_french_light',
                            'fields' => [
                                'stemmed' => [
                                    'type' => 'text',
                                    'analyzer' => 'app_french_heavy',
                                ],
                                'ngram' => [
                                    'type' => 'text',
                                    'analyzer' => 'edge_ngram_analyzer',
                                ],
                            ],
                        ],
                        'classements' => [
                            'properties' => [
                                'name' => [
                                    'type' => 'text',
                                    'analyzer' => 'app_french_light',
                                    'fields' => [
                                        'stemmed' => [
                                            'type' => 'text',
                                            'analyzer' => 'app_french_heavy',
                                        ],
                                        'ngram' => [
                                            'type' => 'text',
                                            'analyzer' => 'edge_ngram_analyzer',
                                        ],
                                    ],
                                ],
                                'description' => [
                                    'type' => 'text',
                                    'analyzer' => 'app_french_light',
                                    'fields' => [
                                        'stemmed' => [
                                            'type' => 'text',
                                            'analyzer' => 'app_french_heavy',
                                        ],
                                        'ngram' => [
                                            'type' => 'text',
                                            'analyzer' => 'edge_ngram_analyzer',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'secteurs' => [
                            'type' => 'text',
                            'analyzer' => 'app_french_light',
                            'fields' => [
                                'stemmed' => [
                                    'type' => 'text',
                                    'analyzer' => 'app_french_heavy',
                                ],
                                'ngram' => [
                                    'type' => 'text',
                                    'analyzer' => 'edge_ngram_analyzer',
                                ],
                            ],
                        ],
                        'name' => [
                            'type' => 'text',
                            'analyzer' => 'app_french_light',
                            'fields' => [
                                'stemmed' => [
                                    'type' => 'text',
                                    'analyzer' => 'app_french_heavy',
                                ],
                                'ngram' => [
                                    'type' => 'text',
                                    'analyzer' => 'edge_ngram_analyzer',
                                ],
                            ],
                        ],
                        'created_at' => [
                            'type' => 'date',
                        ],
                        'updated_at' => [
                            'type' => 'date',
                        ],
                        'image' => [
                            'type' => 'text',
                        ],
                        'email' => [
                            'type' => 'text',
                        ],
                        'contact_email' => [
                            'type' => 'text',
                        ],
                    ],

                ],
            ],
        ];

        return $this->client->indices()->create($params);
    }

    /**
     * @return void
     * @throws ServerResponseException|AuthenticationException|ClientResponseException
     * @throws MissingParameterException
     */
    public function addAll(): void
    {
        $this->connect();
        $this->updateFiches();
        $this->updateCategories();
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function updateFiches(): void
    {
        foreach ($this->ficheRepository->findAllWithJoins() as $fiche) {
            $skip = false;
            foreach ($this->skips as $categoryId) {
                if ($fiche->hasCategory($categoryId)) {
                    $skip = true;
                }
            }

            if ($skip) {
                continue;
            }

            try {
                $this->updateFiche($fiche);
            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }
    }

    private function updateCategories(): void
    {
        foreach ($this->categoryRepository->findAll() as $category) {
            if (\in_array($category->getId(), $this->skips, true)) {
                continue;
            }
            try {
                $this->updateCategorie($category);
            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     * @throws \JsonException
     */
    public function updateFiche(Fiche $fiche): Elasticsearch|\GuzzleHttp\Promise\Promise
    {
        $data = $this->ficheSerializer->serializeFicheForElastic($fiche);
        $data['id'] = $fiche->id;
        $data['type'] = 'fiche';
        $data['classements'] = $this->classementElastic->getClassementsForApi($fiche);
        $data['cap'] = false;
        if ((is_countable($data['classements']) ? \count($data['classements']) : 0) > 0) {
            $data['cap'] = true;
        }

        $data['secteurs'] = $this->classementElastic->getSecteursForApi($data['classements']);

        $params = [
            'index' => $this->indexName,
            'id' => $fiche->id,
            'body' => $data,
        ];
        $this->connect();

        return $this->client->index($params);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     * @throws \JsonException
     */
    public function updateCategorie(Category $category): Elasticsearch|Promise
    {
        $data = $this->categorySerializer->serializeCategory($category);
        $data['type'] = 'category';
        $data['id'] = $category->id;

        $params = [
            'index' => $this->indexName,
            'id' => 'cat_'.$category->id,
            'body' => $data,
        ];

        return $this->client->index($params);
    }

    /**
     * @param int $ficheId
     * @return Elasticsearch|Promise
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function deleteFiche(int $ficheId): Elasticsearch|Promise
    {
        $this->connect();

        return $this->client->delete([$ficheId]);
    }

    /**
     * @return Elasticsearch|Promise
     * @throws ServerResponseException|AuthenticationException|ClientResponseException|MissingParameterException
     */
    function deleteIndex(): Elasticsearch|Promise
    {
        $this->connect();
        $params = ['index' => $this->indexName];

        return $this->client->indices()->delete($params);
    }

    /**
     * @return Elasticsearch|Promise
     * @throws AuthenticationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function settings(): Elasticsearch|Promise
    {
        $this->connect();
        $params = [
            'index' => $this->indexName,
            'body' => [
                'settings' => [
                    'number_of_replicas' => 0,
                    'refresh_interval' => -1,
                ],
            ],
        ];

        return $this->client->indices()->putSettings($params);
    }

}
