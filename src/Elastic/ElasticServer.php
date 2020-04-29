<?php

namespace AcMarche\Bottin\Elastic;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Serializer\CategorySerializer;
use AcMarche\Bottin\Serializer\FicheSerializer;
use AcMarche\Bottin\Utils\FileUtils;
use Elasticsearch\ClientBuilder;

class ElasticServer
{
    use ElasticSearchTrait;

    /**
     * @var \Elasticsearch\Client
     */
    private $client;
    /**
     * @var FileUtils
     */
    private $fileUtils;
    /**
     * @var string
     */
    private $indexName = 'bottin';
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
        FileUtils $fileUtils,
        ClassementElastic $classementElastic,
        FicheSerializer $ficheSerializer,
        CategorySerializer $categorySerializer,
        array $hosts = ['127.0.0.1']
    ) {
        //https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/configuration.html#enabling_logger
        $this->client = ClientBuilder::create()
            ->setHosts(
                $hosts
            )
            ->build();
        $this->fileUtils = $fileUtils;
        $this->ficheSerializer = $ficheSerializer;
        $this->categorySerializer = $categorySerializer;
        $this->classementElastic = $classementElastic;
    }

    /**
     * @throws \Exception
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
        return $this->client->indices()->refresh(['index' => $this->indexName]);
    }

    /**
     * @return array
     *
     */
    function open()
    {
        return $this->client->indices()->open(['index' => $this->indexName]);
    }

    /**
     * @return array
     *
     */
    function close()
    {
        return $this->client->indices()->close(['index' => $this->indexName]);
    }

    /**
     * @return array
     * @throws \Exception
     */
    function createIndex()
    {
        $index = json_decode($this->fileUtils->readConfigFile('schema.json'), true);
        try {
            return $this->client->indices()->create($index);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
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
     * @throws \Exception
     */
    function deleteIndex()
    {
        $params = [
            'index' => $this->indexName,
        ];

        $exist = $this->client->indices()->exists($params);

        if ($exist) {
            try {
                return $this->client->indices()->delete($params);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return true;
    }

    function updateFiche($fiche)
    {
        $std = $this->ficheSerializer->serializeFicheForElastic($fiche);
        $std['classements'] = $this->classementElastic->getClassementsForApi($fiche);
        $std['cap'] = false;
        if (count($std['classements']) > 0) {
            $std['cap'] = true;
        }
        $std['secteurs'] = $this->classementElastic->getSecteursForApi($std['classements']);
        $params = [
            'index' => $this->indexName,
            'id' => $std['id'],
            'body' => $std,
        ];

        return $this->client->index($params);
    }

    function updateCategorie(Category $category)
    {
        $std = $this->categorySerializer->serializeCategory($category);
        $params = [
            'index' => $this->indexName,
            'id' => 'cat_'.$std['id'],
            'body' => $std,
        ];

        return $this->client->index($params);
    }

    function deleteFiche(Fiche $fiche)
    {
        $params = [
            'index' => $this->indexName,
            'id' => $fiche->getId(),
        ];

        $this->client->delete($params);
    }
}
