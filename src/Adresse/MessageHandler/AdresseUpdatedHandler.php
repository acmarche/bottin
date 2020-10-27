<?php

namespace AcMarche\Bottin\Adresse\MessageHandler;

use AcMarche\Bottin\Adresse\Message\AdresseUpdated;
use AcMarche\Bottin\Location\LocationUpdater;
use AcMarche\Bottin\Repository\AdresseRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AdresseUpdatedHandler implements MessageHandlerInterface
{
    private $adresseRepository;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var LocationUpdater
     */
    private $locationUpdater;

    public function __construct(
        AdresseRepository $adresseRepository,
        LocationUpdater $locationUpdater,
        FlashBagInterface $flashBag
    ) {
        $this->adresseRepository = $adresseRepository;
        $this->flashBag = $flashBag;
        $this->locationUpdater = $locationUpdater;
    }

    public function __invoke(AdresseUpdated $adresseCreated)
    {
        $adresse = $this->adresseRepository->find($adresseCreated->getAdresseId());
        $oldRue = $adresseCreated->getOldRue();

        if ($oldRue !== $adresse->getRue()) {
            try {
                $this->locationUpdater->convertAddressToCoordinates($adresse);
                $this->adresseRepository->flush();
            } catch (\Exception $e) {
                $this->flashBag->add(
                    'danger',
                    $e->getMessage()
                );
            }
        }
    }

}
