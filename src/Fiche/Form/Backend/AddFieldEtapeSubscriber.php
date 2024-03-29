<?php

namespace AcMarche\Bottin\Fiche\Form\Backend;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddFieldEtapeSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    public function preSetData(FormEvent $event): void
    {
        $fiche = $event->getData();
        $form = $event->getForm();

        $form
            ->add(
                'etape',
                HiddenType::class,
                [
                ]
            );
    }
}
