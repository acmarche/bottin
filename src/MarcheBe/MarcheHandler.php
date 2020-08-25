<?php

namespace AcMarche\Bottin\MarcheBe;

use AcMarche\Bottin\Classement\Message\ClassementDeleted;
use AcMarche\Bottin\Classement\Message\ClassementUpdated;
use AcMarche\Bottin\Fiche\Message\FicheDeleted;
use AcMarche\Bottin\Fiche\Message\FicheUpdated;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MarcheHandler implements MessageSubscriberInterface
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var FicheRepository
     */
    private $ficheRepository;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(
        HttpClientInterface $httpClient,
        ParameterBagInterface $parameterBag,
        FicheRepository $ficheRepository,
        FlashBagInterface $flashBag
    ) {
        $this->httpClient = $httpClient;
        $this->parameterBag = $parameterBag;
        $this->ficheRepository = $ficheRepository;
        $this->flashBag = $flashBag;
    }

    public function __invoke(ClassementUpdated $classementUpdated)
    {
        $this->sendFiche($classementUpdated->getFicheId());
    }

    public function classementDeleted(ClassementDeleted $classementDeleted)
    {
        $this->sendFiche($classementDeleted->getFicheId());
    }

    public function ficheUpdated(FicheUpdated $ficheCreated)
    {
        $ficheId = $ficheCreated->getFicheId();
        $fiche = $this->ficheRepository->find($ficheId);

        $request = $this->httpClient->request(
            "POST",
            $this->parameterBag->get('bottin.url_update_fiche'),
            [
                'body' => ['ficheid' => $ficheId],
            ]
        );

        try {
            $result = json_decode($request->getContent(), true);
            if (isset($result['error'])) {
                $this->flashBag->add(
                    'danger',
                    "Erreur lors de la mise à jour sur Marche.be: ".$result['error']
                );
            }
        } catch (ClientExceptionInterface $e) {
            $this->flashBag->add(
                'danger',
                "Erreur lors de la mise à jour sur Marche.be: ".$e->getMessage()
            );
        }
    }

    public function ficheDeleted(FicheDeleted $ficheCreated)
    {
        $ficheId = $ficheCreated->getFicheId();
        $fiche = $this->ficheRepository->find($ficheId);
        $request = $this->httpClient->request(
            "POST",
            $this->parameterBag->get('bottin.url_delete_fiche'),
            [
                'body' => ['ficheid' => $ficheId],
            ]
        );

        try {
            $result = json_decode($request->getContent(), true);
            if (isset($result['error'])) {
                $this->flashBag->add(
                    'danger',
                    "Erreur lors de la suppression sur Marche.be: ".$result['error']
                );
            }
        } catch (ClientExceptionInterface $e) {
            $this->flashBag->add(
                'danger',
                "Erreur lors de la suppression sur Marche.be: ".$e->getMessage()
            );
        }
    }

    private function sendFiche(int $ficheId)
    {
        $request = $this->httpClient->request(
            "POST",
            $this->parameterBag->get('bottin.url_update_fiche'),
            [
                'body' => ['ficheid' => $ficheId],
            ]
        );
    }

    public static function getHandledMessages(): iterable
    {
        // handle this message on __invoke
        yield ClassementUpdated::class;

        // also handle this message on handleOtherSmsNotification
        yield ClassementDeleted::class => [
            'method' => 'classementDeleted',
            //'priority' => 0,
            //'bus' => 'messenger.bus.default',
        ];

        yield FicheUpdated::class => [
            'method' => 'ficheUpdated',
        ];

        yield FicheDeleted::class => [
            'method' => 'ficheDeleted',
        ];
    }
}
