<?php

namespace AcMarche\Bottin\Doctrine\EventSubscriber;


use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\History\HistoryUtils;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

//#[AsDoctrineListener(event: Events::preUpdate)]
class ChangeHistorySubscriber
{
    public function __construct(private readonly HistoryUtils $historyUtils)
    {
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Fiche) {
            $changeset = $args->getObjectManager()->getUnitOfWork()->getEntityChangeSet($entity);
            $this->historyUtils->diffFicheNew($entity, $changeset);
        }
    }
}