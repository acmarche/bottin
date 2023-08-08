<?php

namespace AcMarche\Bottin\Localite\MessageHandler;

use AcMarche\Bottin\Localite\Message\LocaliteCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LocaliteCreatedHandler
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(private readonly RequestStack $requestStack)
    {

    }

    public function __invoke(LocaliteCreated $localiteCreated): void
    {
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add(
            'success',
            'La localité a bien été mise à jour'
        );
    }
}
