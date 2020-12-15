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
    )
    {
        $this->ficheRepository = $ficheRepository;
        $this->flashBag = $flashBag;
        $this->elasticServer = $elasticServer;
        $this->locationUpdater = $locationUpdater;
    }

    public function __invoke(FicheUpdated $ficheUpdated)
    {
        $fiche = $this->ficheRepository->find($ficheUpdated->getFicheId());
        if ($this->hasChangeAddress($ficheUpdated, $fiche)) {
            try {
                $this->locationUpdater->convertAddressToCoordinates($fiche);
                $this->ficheRepository->flush();
                $this->flashBag->add('success', 'Coordonnées gps misent à jour');
            } catch (\Exception $e) {
                $this->flashBag->add('danger', $e->getMessage());
            }
        }
        $this->updateSearchEngine($fiche);
    }

    private function updateSearchEngine(Fiche $fiche)
    {
        $this->elasticServer->updateFiche($fiche);
    }

    private function hasChangeAddress(FicheUpdated $ficheUpdated, Fiche $fiche): bool
    {
        $adresse = $fiche->getRue() . ' ' . $fiche->getNumero() . ' ' . $fiche->getLocalite();

        if ($ficheUpdated->getOldAddress() !== $adresse) {
            return true;
        }

        return false;
    }
}
