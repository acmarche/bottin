<?php

namespace AcMarche\Bottin\Category\MessageHandler;

use AcMarche\Bottin\Category\Message\CategoryDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CategoryDeledHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(private RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(CategoryDeleted $categoryDeleted): void
    {
        $this->flashBag->add(
            'success',
            'La catégorie a bien été supprimée'
        );
    }
}
