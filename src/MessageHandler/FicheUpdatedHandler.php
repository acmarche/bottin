<?php

namespace AcMarche\Bottin\MessageHandler;

use AcMarche\Bottin\Elastic\ElasticServer;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Message\FicheUpdated;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Service\GeolocalisationService;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class FicheUpdatedHandler implements MessageHandlerInterface
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
        ElasticServer $elasticServer,
        FlashBagInterface $flashBag
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->geolocalisationService = $geolocalisationService;
        $this->security = $security;
        $this->flashBag = $flashBag;
        $this->elasticServer = $elasticServer;
    }

    public function __invoke(FicheUpdated $ficheCreated)
    {
        $fiche = $this->ficheRepository->find($ficheCreated->getFicheId());
        $this->updateFiche($fiche);
        $oldRue = $ficheCreated->getOldRue();

        if ($oldRue !== $fiche->getRue()) {
            try {
                $this->geolocalisationService->convertToCoordonate($fiche);
            } catch (\Exception $e) {
                $this->flashBag->add(
                    'danger',
                    "Les coordonnées latitude et longitude n'ont pas peu être trouvées: " . $e->getMessage()
                );
            }
        }
    }

    private function updateFiche(Fiche $fiche)
    {
        $this->elasticServer->updateFiche($fiche);
    }
}
