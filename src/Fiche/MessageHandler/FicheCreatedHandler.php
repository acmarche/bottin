<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Elasticsearch\ElasticIndexer;
use AcMarche\Bottin\Fiche\Message\FicheCreated;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Search\MeiliServer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class FicheCreatedHandler
{
    public function __construct(
        private readonly FicheRepository $ficheRepository,
        private readonly MeiliServer $meiliServer,
        private readonly ElasticIndexer $elasticIndexer
    ) {
    }

    public function __invoke(FicheCreated $ficheCreated): void
    {
        $fiche = $this->ficheRepository->find($ficheCreated->getFicheId());
        try {
            $this->elasticIndexer->updateFiche($fiche);
            $this->meiliServer->updateFiche($fiche);
        } catch (\Exception $exception) {

        }
        $this->ficheRepository->flush();
    }
}
