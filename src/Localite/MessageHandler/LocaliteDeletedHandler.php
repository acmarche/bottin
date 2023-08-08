<?php

namespace AcMarche\Bottin\Localite\MessageHandler;

use AcMarche\Bottin\Localite\Message\LocaliteDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class LocaliteDeletedHandler
{

    public function __construct(private readonly RequestStack $requestStack)
    {

    }

    public function __invoke(LocaliteDeleted $localiteDeleted)
    {
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add(
            'success',
            'La localité a bien été mise à jour'
        );
    }
}
