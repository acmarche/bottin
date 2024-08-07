<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Fiche\Message\FicheUpdated;
use AcMarche\Bottin\Location\LocationUpdater;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Search\MeiliServer;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class FicheUpdatedHandler
{
    public function __construct(
        private readonly FicheRepository $ficheRepository,
        private readonly LocationUpdater $locationUpdater,
        private readonly MeiliServer $meiliServer,
        private readonly RequestStack $requestStack
    ) {
    }

    public function __invoke(FicheUpdated $ficheUpdated): void
    {
        $fiche = $this->ficheRepository->find($ficheUpdated->getFicheId());
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        if ($this->hasChangeAddress($ficheUpdated, $fiche)) {
            try {
                $this->locationUpdater->convertAddressToCoordinates($fiche);
                $this->ficheRepository->flush();
                $flashBag->add('success', 'Coordonnées gps misent à jour');
            } catch (\Exception $e) {
                $flashBag->add('danger', $e->getMessage());
            }
        }
        try {
            $this->meiliServer->updateFiche($fiche);
        } catch (\Exception $e) {
            $flashBag->add('danger', 'Erreur indexation moteur de recherche: '.$e->getMessage());
        }
    }

    private function hasChangeAddress(FicheUpdated $ficheUpdated, Fiche $fiche): bool
    {
        $adresse = $fiche->getRue().' '.$fiche->getNumero().' '.$fiche->getLocalite();

        return $ficheUpdated->getOldAddress() !== $adresse;
    }
}
