<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Elasticsearch\ElasticIndexer;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Fiche\Message\FicheCreated;
use AcMarche\Bottin\Location\LocationUpdater;
use AcMarche\Bottin\Repository\FicheRepository;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class FicheCreatedHandler implements MessageSubscriberInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(private FicheRepository $ficheRepository, private LocationUpdater $locationUpdater, private RequestStack $requestStack, private ElasticIndexer $elasticIndexer)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(FicheCreated $ficheCreated): void
    {
        $fiche = $this->ficheRepository->find($ficheCreated->getFicheId());
        $this->setLocation($fiche);
        $this->updateFiche($fiche);
        $this->ficheRepository->flush();
    }

    private function updateFiche(Fiche $fiche): void
    {
        $this->elasticIndexer->updateFiche($fiche);
    }

    private function setLocation(Fiche $fiche): void
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
