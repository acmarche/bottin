<?php

namespace AcMarche\Bottin\Localite\MessageHandler;

use AcMarche\Bottin\Localite\Message\LocaliteDeleted;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class LocaliteDeletedHandler implements MessageHandlerInterface
{
    public function __invoke(LocaliteDeleted $localiteDeleted)
    { $this->flashBag->add(
            'success',
            "La localité a bien été mise à jour"
        );
    }
}
