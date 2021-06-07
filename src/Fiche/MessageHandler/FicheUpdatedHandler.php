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
    private \AcMarche\Bottin\Repository\FicheRepository $ficheRepository;
    private \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface $flashBag;
    private \AcMarche\Bottin\Elastic\ElasticServer $elasticServer;
    private \AcMarche\Bottin\Location\LocationUpdater $locationUpdater;

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

    public function __invoke(FicheUpdated $ficheUpdated): void
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

    private function updateSearchEngine(Fiche $fiche): void
    {
        $this->elasticServer->updateFiche($fiche);
    }

    private function hasChangeAddress(FicheUpdated $ficheUpdated, Fiche $fiche): bool
    {
        $adresse = $fiche->getRue() . ' ' . $fiche->getNumero() . ' ' . $fiche->getLocalite();
        return $ficheUpdated->getOldAddress() !== $adresse;
    }
}
