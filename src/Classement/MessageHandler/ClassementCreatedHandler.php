<?php

namespace AcMarche\Bottin\Classement\MessageHandler;

use AcMarche\Bottin\Classement\Message\ClassementCreated;
use AcMarche\Bottin\History\HistoryUtils;
use AcMarche\Bottin\Repository\ClassementRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ClassementCreatedHandler implements MessageHandlerInterface
{
    private HistoryUtils $historyUtils;
    private ClassementRepository $classementRepository;
    private FlashBagInterface $flashBag;

    public function __construct(
        ClassementRepository $classementRepository,
        HistoryUtils $historyUtils,
        FlashBagInterface $flashBag
    ) {
        $this->historyUtils = $historyUtils;
        $this->classementRepository = $classementRepository;
        $this->flashBag = $flashBag;
    }

    public function __invoke(ClassementCreated $classementCreated): void
    {
        $classement = $this->classementRepository->find($classementCreated->getClassementId());
        $fiche = $classement->getFiche();
        $category = $classement->getCategory();

        $this->flashBag->add('success', 'Le classement a bien été ajouté');

        $this->historyUtils->diffClassement($fiche, $category);
    }
}
