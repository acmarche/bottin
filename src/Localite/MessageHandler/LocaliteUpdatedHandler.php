<?php

namespace AcMarche\Bottin\Localite\MessageHandler;

use AcMarche\Bottin\Localite\Message\LocaliteUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LocaliteUpdatedHandler
{


    public function __construct(private readonly RequestStack $requestStack)
    {

    }

    public function __invoke(LocaliteUpdated $localiteUpdated): void
    {
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add(
            'success',
            'La localité a bien été mise à jour'
        );
    }
}
