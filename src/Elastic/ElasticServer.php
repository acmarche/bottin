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
    public $indexDefinition;
    const INDEX_NAME = 'bottin';

    private \Elasticsearch\Client $client;
    private \AcMarche\Bottin\Utils\FileUtils $fileUtils;
    private \AcMarche\Bottin\Serializer\FicheSerializer $ficheSerializer;
    private \AcMarche\Bottin\Serializer\CategorySerializer $categorySerializer;
    private \AcMarche\Bottin\Elastic\ClassementElastic $classementElastic;

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
    function updateSettingAndMapping(): void
    {
        $this->close();
        $this->updateSettins();
        $this->open();
        $this->updateMappings();
    }

    function razIndex(): void
    {
        $this->deleteIndex();
        $this->createIndex();
    }

    function refresh(): array
    {
        return $this->client->indices()->refresh(['index' => self::INDEX_NAME]);
    }

    function open(): array
    {
        return $this->client->indices()->open(['index' => self::INDEX_NAME]);
    }

    function close(): array
    {
        return $this->client->indices()->close(['index' => self::INDEX_NAME]);
    }

    /**
     * @throws Exception
     */
    function createIndex(): array
    {
        $index = json_decode($this->fileUtils->readConfigFile('schema.json'), true);
        try {
            return $this->client->indices()->create($index);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    function updateSettins(): array
    {
        $settings = json_decode($this->fileUtils->readConfigFile('settings.json'), true);

        return $this->client->indices()->putSettings($settings);
    }

    function updateMappings(): array
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
                throw new Exception($e->getMessage(), $e->getCode(), $e);
            }
        }

        return true;
    }

    function updateFiche($fiche): array
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

    function updateCategorie(Category $category): array
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

    function deleteFiche(Fiche $fiche): void
    {
        $params = [
            'index' => self::INDEX_NAME,
            'id' => $fiche->getId(),
        ];

        $this->client->delete($params);
    }

}
