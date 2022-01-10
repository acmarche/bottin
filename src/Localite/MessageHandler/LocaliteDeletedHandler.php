<?php

namespace AcMarche\Bottin\Localite\MessageHandler;

use AcMarche\Bottin\Localite\Message\LocaliteDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class LocaliteDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(private RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(LocaliteDeleted $localiteDeleted)
    {
        $this->flashBag->add(
            'success',
            'La localité a bien été mise à jour'
        );
    }
}
