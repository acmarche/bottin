<?php

namespace AcMarche\Bottin\Category\MessageHandler;

use AcMarche\Bottin\Category\Message\CategoryDeleted;
use AcMarche\Bottin\Category\Message\CategoryUpdated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CategoryDeledHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(
        FlashBagInterface $flashBag
    ) {
        $this->flashBag = $flashBag;
    }

    public function __invoke(CategoryDeleted $categoryUpdated)
    {
        $this->flashBag->add(
            'success',
            "La catégorie a bien été supprimée"
        );
    }

}
