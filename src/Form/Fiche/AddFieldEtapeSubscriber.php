<?php

namespace AcMarche\Bottin\Form\Fiche;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddFieldEtapeSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    public function preSetData(FormEvent $event)
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
