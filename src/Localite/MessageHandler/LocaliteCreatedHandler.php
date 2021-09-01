<?php

namespace AcMarche\Bottin\Localite\MessageHandler;

use AcMarche\Bottin\Localite\Message\LocaliteCreated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class LocaliteCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(
        FlashBagInterface $flashBag
    ) {
        $this->flashBag = $flashBag;
    }

    public function __invoke(LocaliteCreated $localiteCreated): void
    {
        $this->flashBag->add(
            'success',
            'La localité a bien été mise à jour'
        );
    }
}
