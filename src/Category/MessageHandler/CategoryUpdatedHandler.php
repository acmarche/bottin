<?php

namespace AcMarche\Bottin\Category\MessageHandler;

use AcMarche\Bottin\Category\Message\CategoryUpdated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CategoryUpdatedHandler implements MessageHandlerInterface
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

    public function __invoke(CategoryUpdated $categoryUpdated)
    {
        $this->flashBag->add(
            'success',
            "La catégorie a bien été mise à jour"
        );
    }

}
