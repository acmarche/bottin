<?php

namespace AcMarche\Bottin\Category\MessageHandler;

use AcMarche\Bottin\Category\Message\CategoryCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CategoryCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(CategoryCreated $categoryCreated): void
    {
        $this->flashBag->add(
            'success',
            'La catégorie a bien été crée'
        );
    }
}
