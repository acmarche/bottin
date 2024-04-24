<?php

namespace AcMarche\Bottin\Elasticsearch;


use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Serializer\CategorySerializer;
use AcMarche\Bottin\Serializer\FicheSerializer;
use AcMarche\Bottin\Utils\FileUtils;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;

trait ElasticClientTrait
{
    public ?Client $client = null;
    private Promise|Elasticsearch $index;

    public function __construct(
        #[Autowire(env: 'ELASTIC_INDEX_NAME')] private readonly string $indexName,
        #[Autowire(env: 'ELASTIC_USER')] private readonly string $elasticUser,
        #[Autowire(env: 'ELASTIC_CRT_PATH')] private readonly string $caCrtPath,
        private readonly FicheRepository $ficheRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly SerializerInterface $serializer,
        private readonly FicheSerializer $ficheSerializer,
        private readonly CategorySerializer $categorySerializer,
        private readonly ClassementElastic $classementElastic,
        private readonly FileUtils $fileUtils,
        private readonly LoggerInterface $logger
    ) {

    }

    /**
     * @return void
     * @throws AuthenticationException
     */
    public function connect(): void
    {
        if (!$this->client) {
            $this->client = ClientBuilder::create()
                ->setHosts(['https://localhost:9200'])
                ->setBasicAuthentication('elastic', $this->elasticUser)
                ->setCABundle($this->caCrtPath)
                ->setLogger($this->logger)
                ->build();
        }
    }
}
