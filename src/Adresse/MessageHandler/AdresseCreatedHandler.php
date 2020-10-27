<?php

namespace AcMarche\Bottin\Adresse\MessageHandler;

use AcMarche\Bottin\Adresse\Message\AdresseCreated;
use AcMarche\Bottin\Entity\Adresse;
use AcMarche\Bottin\Location\LocationUpdater;
use AcMarche\Bottin\Repository\AdresseRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class AdresseCreatedHandler implements MessageSubscriberInterface
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

    public function __invoke(AdresseCreated $adresseCreated)
    {
        $adresse = $this->adresseRepository->find($adresseCreated->getAdresseId());
        $this->setLocation($adresse);
        $this->adresseRepository->flush();
    }

    private function setLocation(Adresse $adresse)
    {
        try {
            $this->locationUpdater->convertAddressToCoordinates($adresse);
        } catch (\Exception $e) {
            $this->flashBag->add(
                'danger',
                $e->getMessage()
            );
        }
    }

    /**
     * @inheritDoc
     */
    public static function getHandledMessages(): iterable
    {
        // handle this message on __invoke
        yield AdresseCreated::class;

        // also handle this message on handleOtherSmsNotification
        yield AdresseCreated::class => [
            //  'method' => 'handleElastic',
            //'priority' => 0,
            //'bus' => 'messenger.bus.default',
        ];
    }
}
