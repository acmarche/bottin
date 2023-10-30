<?php

namespace AcMarche\Bottin\Fiche\Form\Backend;

use AcMarche\Bottin\Meta\Form\MetaDataType;
use AcMarche\Bottin\Meta\Repository\MetaFieldRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddFieldsMetaSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MetaFieldRepository $metaFieldRepository)
    {
    }

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
                'metas',
                CollectionType::class,
                [
                    'entry_type' => MetaDataType::class,
                ]
            );
    }
}
