<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Elastic\ElasticServer;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Fiche\Message\FicheCreated;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Service\GeolocalisationService;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Security\Core\Security;

use function Ramsey\Uuid\v1;

class FicheCreatedHandler implements MessageSubscriberInterface
{
    private $ficheRepository;
    /**
     * @var GeolocalisationService
     */
    private $geolocalisationService;
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

    public function __construct(
        FicheRepository $ficheRepository,
        GeolocalisationService $geolocalisationService,
        Security $security,
        FlashBagInterface $flashBag,
        ElasticServer $elasticServer
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->geolocalisationService = $geolocalisationService;
        $this->security = $security;
        $this->flashBag = $flashBag;
        $this->elasticServer = $elasticServer;
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
            $this->geolocalisationService->convertToCoordonate($fiche);
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
