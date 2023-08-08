<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Elasticsearch\ElasticIndexer;
use AcMarche\Bottin\Fiche\Message\FicheCreated;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class FicheCreatedHandler
{
    public function __construct(
        private readonly FicheRepository $ficheRepository,
        private readonly ElasticIndexer $elasticIndexer
    ) {

    }

    public function __invoke(FicheCreated $ficheCreated): void
    {
        $fiche = $this->ficheRepository->find($ficheCreated->getFicheId());
        $this->elasticIndexer->updateFiche($fiche);
        $this->ficheRepository->flush();
    }

}
