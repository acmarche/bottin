<?php

namespace AcMarche\Bottin\Adresse\MessageHandler;

use AcMarche\Bottin\Adresse\Message\AdresseUpdated;
use AcMarche\Bottin\Location\LocationUpdater;
use AcMarche\Bottin\Repository\AdresseRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AdresseUpdatedHandler
{
    public $flashBag;

    public function __construct(
        private readonly AdresseRepository $adresseRepository,
        private readonly LocationUpdater $locationUpdater,
        private readonly RequestStack $requestStack
    ) {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(AdresseUpdated $adresseUpdated): void
    {
        $adresse = $this->adresseRepository->find($adresseUpdated->getAdresseId());
        $oldRue = $adresseUpdated->getOldRue();

        if ($oldRue !== $adresse->getRue()) {
            try {
                $this->locationUpdater->convertAddressToCoordinates($adresse);
                $this->adresseRepository->flush();
            } catch (\Exception $e) {
                $flashBag = $this->requestStack->getSession()->getFlashBag();
                $flashBag->add(
                    'danger',
                    $e->getMessage()
                );
            }
        }
    }
}
