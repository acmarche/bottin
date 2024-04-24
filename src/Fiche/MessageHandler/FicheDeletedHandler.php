<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Elasticsearch\ElasticServer;
use AcMarche\Bottin\Fiche\Message\FicheDeleted;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Search\MeiliServer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class FicheDeletedHandler
{
    public function __construct(
        private readonly FicheRepository $ficheRepository,
        private readonly ElasticServer $elasticIndexer,
        private readonly MeiliServer $meiliServer
    ) {
    }

    public function __invoke(FicheDeleted $ficheDeleted): void
    {
        $fiche = $this->ficheRepository->find($ficheDeleted->getFicheId());
        $this->elasticIndexer->deleteFiche($fiche);
        $this->meiliServer->removeFiche($ficheDeleted->getFicheId());
    }
}
