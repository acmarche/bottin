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

    }

    public function classementDeleted(ClassementDeleted $classementDeleted)
    {

    }

    public function ficheUpdated(FicheUpdated $ficheCreated)
    {

    }

    public function ficheDeleted(FicheDeleted $ficheCreated)
    {

    }

    private function sendFiche(int $ficheId)
    {

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
