<?php

namespace AcMarche\Bottin\MarcheBe;

use AcMarche\Bottin\Classement\Message\ClassementDeleted;
use AcMarche\Bottin\Classement\Message\ClassementUpdated;
use AcMarche\Bottin\Fiche\Message\FicheDeleted;
use AcMarche\Bottin\Fiche\Message\FicheUpdated;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class MarcheHandler implements MessageSubscriberInterface
{
    public function __construct()
    {
    }

    public function __invoke()
    {
    }

    public function classementDeleted(): void
    {
    }

    public function ficheUpdated(): void
    {
    }

    public function ficheDeleted(): void
    {
    }

    public static function getHandledMessages(): iterable
    {
        // handle this message on __invoke
        yield ClassementUpdated::class;

        // also handle this message on handleOtherSmsNotification
        yield ClassementDeleted::class => [
            'method' => 'classementDeleted',
            // 'priority' => 0,
            // 'bus' => 'messenger.bus.default',
        ];

        yield FicheUpdated::class => [
            'method' => 'ficheUpdated',
        ];

        yield FicheDeleted::class => [
            'method' => 'ficheDeleted',
        ];
    }
}
