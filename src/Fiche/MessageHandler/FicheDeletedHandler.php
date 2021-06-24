<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Elasticsearch\ElasticIndexer;
use AcMarche\Bottin\Fiche\Message\FicheDeleted;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class FicheDeletedHandler implements MessageHandlerInterface
{
    private FicheRepository $ficheRepository;
    private ElasticIndexer $elasticIndexer;

    public function __construct(
        FicheRepository $ficheRepository,
        ElasticIndexer $elasticIndexer
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->elasticIndexer = $elasticIndexer;
    }

    public function __invoke(FicheDeleted $ficheDeleted): void
    {
        $fiche = $this->ficheRepository->find($ficheDeleted->getFicheId());
        $this->elasticIndexer->deleteFiche($fiche);
    }
}
