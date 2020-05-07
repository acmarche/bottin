<?php

namespace AcMarche\Bottin\Category\MessageHandler;

use AcMarche\Bottin\Category\Message\CategoryUpdated;
use AcMarche\Bottin\Elastic\ElasticServer;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Service\GeolocalisationService;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CategoryUpdatedHandler implements MessageHandlerInterface
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

    public function __invoke(CategoryUpdated $categoryUpdated)
    {
        $this->flashBag->add(
            'success',
            "La catégorie a bien été mise à jour"
        );
    }

}
