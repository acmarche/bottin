<?php

namespace AcMarche\Bottin\Classement\MessageHandler;

use AcMarche\Bottin\Classement\Message\ClassementDeleted;
use AcMarche\Bottin\History\HistoryUtils;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ClassementDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(private HistoryUtils $historyUtils, RequestStack $requestStack, private CategoryRepository $categoryRepository, private FicheRepository $ficheRepository)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(ClassementDeleted $classementDeleted): void
    {
        $category = $this->categoryRepository->find($classementDeleted->getCategoryId());
        $fiche = $this->ficheRepository->find($classementDeleted->getFicheId());

        $this->historyUtils->diffClassement($fiche, $category, 'suppression');
        $this->flashBag->add('success', 'Le classement a bien été supprimé');
    }
}
