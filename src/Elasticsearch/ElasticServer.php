<?php

namespace AcMarche\Bottin\Elasticsearch;

use AcMarche\Bottin\Utils\FileUtils;
use Elastica\Mapping;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * https://elasticsearch-cheatsheet.jolicode.com/
 * Class ElasticServer.
 */
class ElasticServer
{
    use ElasticClientTrait;

    final public const INDEX_NAME = 'bottin';

    public function __construct(string $elasticIndexName, private readonly FileUtils $fileUtils)
    {
        $this->connect($elasticIndexName);
    }

    public function createIndex(): void
    {
        try {
            $analyser = $this->fileUtils->readConfigFile('analyzers.yaml');
            $settings = $this->fileUtils->readConfigFile('settings.yaml');
        } catch (ParseException $parseException) {
            printf('Unable to parse the YAML string: %s', $parseException->getMessage());

            return;
        }

        $settings['settings']['analysis'] = $analyser;
        $response = $this->index->create($settings, true);
        dump($response);
    }

    public function setMapping(): void
    {
        try {
            $properties = $this->fileUtils->readConfigFile('mapping.yaml');
            //$properties = Yaml::parse(file_get_contents(__DIR__.'/mappings/mapping.yaml'));
            $mapping = new Mapping($properties['mappings']['properties']);
            $response = $this->index->setMapping($mapping);
            dump($response);
        } catch (ParseException $parseException) {
            printf('Unable to parse the YAML string: %s', $parseException->getMessage());
        }
    }
}
