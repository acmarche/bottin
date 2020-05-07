<?php

namespace AcMarche\Bottin\MarcheBe;

use AcMarche\Bottin\Classement\Message\ClassementDeleted;
use AcMarche\Bottin\Classement\Message\ClassementUpdated;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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

    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $parameterBag)
    {
        $this->httpClient = $httpClient;
        $this->parameterBag = $parameterBag;
    }

    public function __invoke(ClassementUpdated $classementUpdated)
    {
        $this->sendFiche($classementUpdated->getFicheId());
    }

    public function classementDeleted(ClassementDeleted $classementDeleted)
    {
        $this->sendFiche($classementDeleted->getFicheId());
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
    }
}
