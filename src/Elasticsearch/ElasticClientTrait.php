<?php

namespace AcMarche\Bottin\Elasticsearch;

use Elastica\Client;
use Elastica\Index;

trait ElasticClientTrait
{
    public Client $client;
    private Index $index;

    public function connect(string $indexName, int $port = 9200)
    {
        $username = $_ENV['ELASTIC_USER'];
        $password = $_ENV['ELASTIC_PASSWORD'];
        $ds = $username.':'.$password.'@localhost';
        $this->client = new Client(
            [
                'host' => $ds,
                'port' => $port,
            ]
        );
        $this->setIndex($indexName);
    }

    public function setIndex(string $name)
    {
        $this->index = $this->client->getIndex($name);
    }
}
