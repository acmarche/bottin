<?php

namespace AcMarche\Bottin\Classement\MessageHandler;

use AcMarche\Bottin\Classement\Message\ClassementDeleted;
use AcMarche\Bottin\History\HistoryUtils;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ClassementDeletedHandler implements MessageHandlerInterface
{
    private HistoryUtils $historyUtils;
    private FlashBagInterface $flashBag;
    private CategoryRepository $categoryRepository;
    private FicheRepository $ficheRepository;

    public function __construct(
        HistoryUtils $historyUtils,
        FlashBagInterface $flashBag,
        CategoryRepository $categoryRepository,
        FicheRepository $ficheRepository
    ) {
        $this->historyUtils = $historyUtils;
        $this->flashBag = $flashBag;
        $this->categoryRepository = $categoryRepository;
        $this->ficheRepository = $ficheRepository;
    }

    public function __invoke(ClassementDeleted $classementDeleted): void
    {
        $category = $this->categoryRepository->find($classementDeleted->getCategoryId());
        $fiche = $this->ficheRepository->find($classementDeleted->getFicheId());

        $this->historyUtils->diffClassement($fiche, $category);
        $this->flashBag->add('success', 'Le classement a bien été supprimé');
    }
}
