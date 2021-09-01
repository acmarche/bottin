<?php

namespace AcMarche\Bottin\Localite\MessageHandler;

use AcMarche\Bottin\Localite\Message\LocaliteUpdated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class LocaliteUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(
        FlashBagInterface $flashBag
    ) {
        $this->flashBag = $flashBag;
    }

    public function __invoke(LocaliteUpdated $localiteUpdated): void
    {
        $this->flashBag->add(
            'success',
            'La localité a bien été mise à jour'
        );
    }
}
