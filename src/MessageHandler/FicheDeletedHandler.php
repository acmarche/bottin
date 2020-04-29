<?php

namespace AcMarche\Bottin\MessageHandler;

use AcMarche\Bottin\Elastic\ElasticServer;
use AcMarche\Bottin\Message\FicheDeleted;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class FicheDeletedHandler implements MessageHandlerInterface
{
    /**
     * @var ElasticServer
     */
    private $elasticServer;
    /**
     * @var FicheRepository
     */
    private $ficheRepository;

    public function __construct(
        ElasticServer $elasticServer,
        FicheRepository $ficheRepository
    ) {
        $this->elasticServer = $elasticServer;
        $this->ficheRepository = $ficheRepository;
    }

    public function __invoke(FicheDeleted $ficheDeleted)
    {
        $fiche = $this->ficheRepository->find($ficheDeleted->getFicheId());
        $this->elasticServer->deleteFiche($fiche);
    }
}
