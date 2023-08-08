<?php

namespace AcMarche\Bottin\Category\MessageHandler;

use AcMarche\Bottin\Category\Message\CategoryCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CategoryCreatedHandler
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function __invoke(CategoryCreated $categoryCreated): void
    {
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add(
            'success',
            'La catégorie a bien été crée'
        );
    }
}
