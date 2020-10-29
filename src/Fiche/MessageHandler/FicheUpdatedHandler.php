<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Elastic\ElasticServer;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Fiche\Message\FicheUpdated;
use AcMarche\Bottin\Location\LocationUpdater;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FicheUpdatedHandler implements MessageHandlerInterface
{
    private $ficheRepository;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var ElasticServer
     */
    private $elasticServer;
    /**
     * @var LocationUpdater
     */
    private $locationUpdater;

    public function __construct(
        FicheRepository $ficheRepository,
        LocationUpdater $locationUpdater,
        ElasticServer $elasticServer,
        FlashBagInterface $flashBag
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->flashBag = $flashBag;
        $this->elasticServer = $elasticServer;
        $this->locationUpdater = $locationUpdater;
    }

    public function __invoke(FicheUpdated $ficheCreated)
    {
        $fiche = $this->ficheRepository->find($ficheCreated->getFicheId());
        $this->updateFiche($fiche);
        $oldRue = $ficheCreated->getOldRue();

        if ($oldRue !== $fiche->getRue()) {
            try {
                $this->locationUpdater->convertAddressToCoordinates($fiche);
            } catch (\Exception $e) {
                $this->flashBag->add('danger', $e->getMessage());
            }
        }
    }

    private function updateFiche(Fiche $fiche)
    {
        $this->elasticServer->updateFiche($fiche);
    }


}
