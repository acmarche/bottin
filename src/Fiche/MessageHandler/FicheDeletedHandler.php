<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Elastic\ElasticServer;
use AcMarche\Bottin\Fiche\Message\FicheDeleted;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class FicheDeletedHandler implements MessageHandlerInterface
{
    private ElasticServer $elasticServer;
    private FicheRepository $ficheRepository;

    public function __construct(
        ElasticServer $elasticServer,
        FicheRepository $ficheRepository
    ) {
        $this->elasticServer = $elasticServer;
        $this->ficheRepository = $ficheRepository;
    }

    public function __invoke(FicheDeleted $ficheDeleted): void
    {
        $fiche = $this->ficheRepository->find($ficheDeleted->getFicheId());
        $this->elasticServer->deleteFiche($fiche);
    }
}
