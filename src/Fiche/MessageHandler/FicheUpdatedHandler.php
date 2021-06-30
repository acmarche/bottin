<?php

namespace AcMarche\Bottin\Fiche\MessageHandler;

use AcMarche\Bottin\Elasticsearch\ElasticIndexer;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Fiche\Message\FicheUpdated;
use AcMarche\Bottin\History\HistoryUtils;
use AcMarche\Bottin\Location\LocationUpdater;
use AcMarche\Bottin\Repository\FicheRepository;
use Exception;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FicheUpdatedHandler implements MessageHandlerInterface
{
    private FicheRepository $ficheRepository;
    private FlashBagInterface $flashBag;
    private LocationUpdater $locationUpdater;
    private ElasticIndexer $elasticIndexer;
    private HistoryUtils $historyUtils;

    public function __construct(
        FicheRepository $ficheRepository,
        LocationUpdater $locationUpdater,
        ElasticIndexer $elasticIndexer,
        FlashBagInterface $flashBag,
        HistoryUtils $historyUtils
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->flashBag = $flashBag;
        $this->locationUpdater = $locationUpdater;
        $this->elasticIndexer = $elasticIndexer;
        $this->historyUtils = $historyUtils;
    }

    public function __invoke(FicheUpdated $ficheUpdated): void
    {
        $fiche = $this->ficheRepository->find($ficheUpdated->getFicheId());
        if ($this->hasChangeAddress($ficheUpdated, $fiche)) {
            try {
                $this->locationUpdater->convertAddressToCoordinates($fiche);
                $this->ficheRepository->flush();
                $this->flashBag->add('success', 'Coordonnées gps misent à jour');
            } catch (Exception $e) {
                $this->flashBag->add('danger', $e->getMessage());
            }
        }
        $this->updateSearchEngine($fiche);
        $this->history($fiche);
    }

    private function updateSearchEngine(Fiche $fiche): void
    {
        $this->elasticIndexer->updateFiche($fiche);
    }

    private function hasChangeAddress(FicheUpdated $ficheUpdated, Fiche $fiche): bool
    {
        $adresse = $fiche->getRue().' '.$fiche->getNumero().' '.$fiche->getLocalite();

        return $ficheUpdated->getOldAddress() !== $adresse;
    }

    private function history(Fiche $fiche)
    {
        $this->historyUtils->diffFiche($fiche);
    }
}
