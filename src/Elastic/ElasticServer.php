<?php

namespace AcMarche\Bottin\Elastic;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Serializer\CategorySerializer;
use AcMarche\Bottin\Serializer\FicheSerializer;
use AcMarche\Bottin\Utils\FileUtils;
use Elasticsearch\Client;
use Exception;

class ElasticServer
{
    const INDEX_NAME = 'bottin';

    /**
     * @var Client
     */
    private $client;
    /**
     * @var FileUtils
     */
    private $fileUtils;
    /**
     * @var FicheSerializer
     */
    private $ficheSerializer;
    /**
     * @var CategorySerializer
     */
    private $categorySerializer;
    /**
     * @var ClassementElastic
     */
    private $classementElastic;

    public function __construct(
        Client $client,
        FileUtils $fileUtils,
        ClassementElastic $classementElastic,
        FicheSerializer $ficheSerializer,
        CategorySerializer $categorySerializer
    ) {
        //https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/configuration.html#enabling_logger
        $this->fileUtils = $fileUtils;
        $this->ficheSerializer = $ficheSerializer;
        $this->categorySerializer = $categorySerializer;
        $this->classementElastic = $classementElastic;
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    function updateSettingAndMapping()
    {
        $this->close();
        $this->updateSettins();
        $this->open();
        $this->updateMappings();
    }

    /**
     *
     */
    function razIndex()
    {
        $this->deleteIndex();
        $this->createIndex();
    }

    /**
     * @return array
     *
     */
    function refresh()
    {
        return $this->client->indices()->refresh(['index' => self::INDEX_NAME]);
    }

    /**
     * @return array
     *
     */
    function open()
    {
        return $this->client->indices()->open(['index' => self::INDEX_NAME]);
    }

    /**
     * @return array
     *
     */
    function close()
    {
        return $this->client->indices()->close(['index' => self::INDEX_NAME]);
    }

    /**
     * @return array
     * @throws Exception
     */
    function createIndex()
    {
        $index = json_decode($this->fileUtils->readConfigFile('schema.json'), true);
        try {
            return $this->client->indices()->create($index);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @return array
     */
    function updateSettins()
    {
        $settings = json_decode($this->fileUtils->readConfigFile('settings.json'), true);

        return $this->client->indices()->putSettings($settings);
    }

    /**
     * @return array
     */
    function updateMappings()
    {
        $mappings = json_decode($this->fileUtils->readConfigFile('mappings.json'), true);

        return $this->client->indices()->putMapping($mappings);
    }

    /**
     * @return array|bool
     * @throws Exception
     */
    function deleteIndex()
    {
        $params = [
            'index' => self::INDEX_NAME,
        ];

        $exist = $this->client->indices()->exists($params);

        if ($exist) {
            try {
                return $this->client->indices()->delete($params);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        return true;
    }

    function updateFiche($fiche)
    {
        $data = $this->ficheSerializer->serializeFicheForElastic($fiche);
        $data['type'] = 'fiche';
        $data['classements'] = $this->classementElastic->getClassementsForApi($fiche);
        $data['cap'] = false;
        if (count($data['classements']) > 0) {
            $data['cap'] = true;
        }
        $data['secteurs'] = $this->classementElastic->getSecteursForApi($data['classements']);
        $params = [
            'index' => self::INDEX_NAME,
            'id' => $data['id'],
            'body' => $data,
        ];

        return $this->client->index($params);
    }

    function updateCategorie(Category $category)
    {
        $data = $this->categorySerializer->serializeCategory($category);
        $data['type'] = 'category';

        $params = [
            'index' => self::INDEX_NAME,
            'id' => 'cat_'.$data['id'],
            'body' => $data,
        ];

        return $this->client->index($params);
    }

    function deleteFiche(Fiche $fiche)
    {
        $params = [
            'index' => self::INDEX_NAME,
            'id' => $fiche->getId(),
        ];

        $this->client->delete($params);
    }

    /**
     * Creates index with mapping and analyzer.
     * https://medium.com/@stefan.poeltl/symfony-meets-elasticsearch-implement-a-search-as-you-type-feature-307e2244f078
     */
    private function createIndex22(): void
    {
        if ($this->client->indices()->exists($this->indexDefinition)) {
            $this->client->indices()->delete($this->indexDefinition);
        }

        $this->client->indices()->create(
            array_merge(
                $this->indexDefinition,
                [
                    'body' => [
                        'settings' => [
                            'number_of_shards' => 1,
                            'number_of_replicas' => 0,
                            "analysis" => [
                                "analyzer" => [
                                    "autocomplete" => [
                                        "tokenizer" => "autocomplete",
                                        "filter" => ["lowercase"],
                                    ],
                                ],
                                "tokenizer" => [
                                    "autocomplete" => [
                                        "type" => "edge_ngram",
                                        "min_gram" => 2,
                                        "max_gram" => 20,
                                        "token_chars" => [
                                            "letter",
                                            "digit",
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        "mappings" => [
                            "properties" => [
                                "title" => [
                                    "type" => "text",
                                    "analyzer" => "autocomplete",
                                    "search_analyzer" => "standard",
                                ],
                            ],
                        ],
                    ],
                ]
            )
        );
    }

}
