<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Elasticsearch\ElasticIndexer;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Fiche\Message\FicheUpdated;
use AcMarche\Bottin\Location\LocationUpdater;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class FicheUpdatedHandler
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(
        private readonly FicheRepository $ficheRepository,
        private readonly LocationUpdater $locationUpdater,
        private readonly ElasticIndexer $elasticIndexer,
        private readonly RequestStack $requestStack
    ) {
    }

    public function __invoke(FicheUpdated $ficheUpdated): void
    {
        $fiche = $this->ficheRepository->find($ficheUpdated->getFicheId());
        $this->flashBag = $this->requestStack->getSession()->getFlashBag();
        if ($this->hasChangeAddress($ficheUpdated, $fiche)) {
            try {
                $this->locationUpdater->convertAddressToCoordinates($fiche);
                $this->ficheRepository->flush();
                $this->flashBag->add('success', 'Coordonnées gps misent à jour');
            } catch (\Exception $e) {
                $this->flashBag->add('danger', $e->getMessage());
            }
        }
        try {
            $this->elasticIndexer->updateFiche($fiche);
        } catch (\Exception $e) {
            $this->flashBag->add('danger', 'Erreur indexation moteur de recherche: '.$e->getMessage());
        }
    }

    private function hasChangeAddress(FicheUpdated $ficheUpdated, Fiche $fiche): bool
    {
        $adresse = $fiche->getRue().' '.$fiche->getNumero().' '.$fiche->getLocalite();

        return $ficheUpdated->getOldAddress() !== $adresse;
    }
}
