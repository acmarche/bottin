<?php

namespace AcMarche\Bottin\Category\MessageHandler;

use AcMarche\Bottin\Category\Message\CategoryUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CategoryUpdatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(private RequestStack $requestStack)
    {

    }

    public function __invoke(CategoryUpdated $categoryUpdated): void
    {
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add(
            'success',
            'La catégorie a bien été mise à jour'
        );
    }
}
