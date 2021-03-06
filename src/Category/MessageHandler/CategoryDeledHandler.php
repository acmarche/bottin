<?php

namespace AcMarche\Bottin\Category\MessageHandler;

use AcMarche\Bottin\Category\Message\CategoryDeleted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CategoryDeledHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(
        FlashBagInterface $flashBag
    ) {
        $this->flashBag = $flashBag;
    }

    public function __invoke(CategoryDeleted $categoryDeleted): void
    {
        $this->flashBag->add(
            'success',
            "La catégorie a bien été supprimée"
        );
    }
}
