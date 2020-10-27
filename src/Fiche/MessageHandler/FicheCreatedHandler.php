<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Elastic\ElasticServer;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Fiche\Message\FicheCreated;
use AcMarche\Bottin\Location\LocationUpdater;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class FicheCreatedHandler implements MessageSubscriberInterface
{
    private $ficheRepository;
    /**
     * @var Security
     */
    private $security;
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
        Security $security,
        FlashBagInterface $flashBag,
        ElasticServer $elasticServer
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->security = $security;
        $this->flashBag = $flashBag;
        $this->elasticServer = $elasticServer;
        $this->locationUpdater = $locationUpdater;
    }

    public function __invoke(FicheCreated $ficheCreated)
    {
        $fiche = $this->ficheRepository->find($ficheCreated->getFicheId());
        $this->setUserAdd($fiche);
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
        } catch (\Exception $e) {
            $this->flashBag->add(
                'danger',
                "Les coordonnées latitude et longitude n'ont pas peu être trouvées: ".$e->getMessage()
            );
        }
    }

    private function setUserAdd(Fiche $fiche)
    {
        $user = $this->security->getUser();
        if ($user) {
            $fiche->setUser($user->getUsername());
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
