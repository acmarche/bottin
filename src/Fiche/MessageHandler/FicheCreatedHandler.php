<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Elastic\ElasticServer;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Fiche\Message\FicheCreated;
use AcMarche\Bottin\Location\LocationUpdater;
use AcMarche\Bottin\Repository\FicheRepository;
use Exception;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class FicheCreatedHandler implements MessageSubscriberInterface
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
        FlashBagInterface $flashBag,
        ElasticServer $elasticServer
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->flashBag = $flashBag;
        $this->elasticServer = $elasticServer;
        $this->locationUpdater = $locationUpdater;
    }

    public function __invoke(FicheCreated $ficheCreated)
    {
        $fiche = $this->ficheRepository->find($ficheCreated->getFicheId());
        $this->setLocation($fiche);
        $this->updateFiche($fiche);
        $this->ficheRepository->flush();
    }

    private function updateFiche(Fiche $fiche)
    {
        $this->elasticServer->updateFiche($fiche);
    }

    private function setLocation(Fiche $fiche)
    {
        try {
            $this->locationUpdater->convertAddressToCoordinates($fiche);
        } catch (Exception $e) {
            $this->flashBag->add('danger', $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public static function getHandledMessages(): iterable
    {
        // handle this message on __invoke
        yield FicheCreated::class;

        // also handle this message on handleOtherSmsNotification
        yield FicheCreated::class => [
            //  'method' => 'handleElastic',
            //'priority' => 0,
            //'bus' => 'messenger.bus.default',
        ];
    }
}
