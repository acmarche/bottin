<?php

namespace AcMarche\Bottin\Classement\MessageHandler;

use AcMarche\Bottin\Classement\Message\ClassementCreated;
use AcMarche\Bottin\History\HistoryUtils;
use AcMarche\Bottin\Repository\ClassementRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ClassementCreatedHandler
{
    public function __construct(
        private readonly ClassementRepository $classementRepository,
        private readonly HistoryUtils $historyUtils,
        private readonly RequestStack $requestStack
    ) {

    }

    public function __invoke(ClassementCreated $classementCreated): void
    {
        $classement = $this->classementRepository->find($classementCreated->getClassementId());
        $fiche = $classement->getFiche();
        $category = $classement->getCategory();
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add('success', 'Le classement a bien été ajouté');

        $this->historyUtils->diffClassement($fiche, $category, 'ajout');
    }
}
