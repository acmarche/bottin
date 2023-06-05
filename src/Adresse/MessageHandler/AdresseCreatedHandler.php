<?php

namespace AcMarche\Bottin\Adresse\MessageHandler;

use AcMarche\Bottin\Adresse\Message\AdresseCreated;
use AcMarche\Bottin\Entity\Adresse;
use AcMarche\Bottin\Location\LocationUpdater;
use AcMarche\Bottin\Repository\AdresseRepository;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AdresseCreatedHandler
{
    public function __construct(
        private AdresseRepository $adresseRepository,
        private LocationUpdater $locationUpdater,
        private RequestStack $requestStack
    ) {

    }

    public function __invoke(AdresseCreated $adresseCreated): void
    {
        $adresse = $this->adresseRepository->find($adresseCreated->getAdresseId());
        $this->setLocation($adresse);
        $this->adresseRepository->flush();
    }

    private function setLocation(Adresse $adresse): void
    {
        try {
            $this->locationUpdater->convertAddressToCoordinates($adresse);
        } catch (Exception $e) {
            $flashBag = $this->requestStack->getSession()->getFlashBag();
            $flashBag->add(
                'danger',
                $e->getMessage()
            );
        }
    }

}
