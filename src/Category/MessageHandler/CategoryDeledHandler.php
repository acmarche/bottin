<?php

namespace AcMarche\Bottin\Category\MessageHandler;

use AcMarche\Bottin\Category\Message\CategoryDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CategoryDeledHandler
{
    public function __construct(private RequestStack $requestStack)
    {

    }

    public function __invoke(CategoryDeleted $categoryDeleted): void
    {
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add(
            'success',
            'La catégorie a bien été supprimée'
        );
    }
}
