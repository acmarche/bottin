<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Elasticsearch\ElasticServer;
use AcMarche\Bottin\Fiche\Message\FicheDeleted;
use AcMarche\Bottin\Search\MeiliServer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\String\ByteString;

#[AsMessageHandler]
final class FicheDeletedHandler
{
    public function __construct(
        private readonly ElasticServer $elasticIndexer,
        private readonly MeiliServer $meiliServer
    ) {
    }

    public function __invoke(FicheDeleted $ficheDeleted): void
    {
        try {
            $this->elasticIndexer->deleteFiche($ficheDeleted->getFicheId());
            $this->meiliServer->removeFiche($ficheDeleted->getFicheId());
        } catch (\Exception $e) {

        }
    }
}
