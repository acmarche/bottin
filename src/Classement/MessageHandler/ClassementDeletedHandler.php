<?php

namespace AcMarche\Bottin\Classement\MessageHandler;

use AcMarche\Bottin\Classement\Message\ClassementDeleted;
use AcMarche\Bottin\History\HistoryUtils;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ClassementDeletedHandler
{

    public function __construct(
        private HistoryUtils $historyUtils,
        private RequestStack $requestStack,
        private CategoryRepository $categoryRepository,
        private FicheRepository $ficheRepository
    ) {

    }

    public function __invoke(ClassementDeleted $classementDeleted): void
    {
        $category = $this->categoryRepository->find($classementDeleted->getCategoryId());
        $fiche = $this->ficheRepository->find($classementDeleted->getFicheId());

        $this->historyUtils->diffClassement($fiche, $category, 'suppression');

        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add('success', 'Le classement a bien été supprimé');
    }
}
