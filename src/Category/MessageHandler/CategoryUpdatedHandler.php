<?php

namespace AcMarche\Bottin\Category\MessageHandler;

use AcMarche\Bottin\Category\Message\CategoryUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CategoryUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(private RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(CategoryUpdated $categoryUpdated): void
    {
        $this->flashBag->add(
            'success',
            'La catégorie a bien été mise à jour'
        );
    }
}
