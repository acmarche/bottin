<?php

namespace AcMarche\Bottin\Form;

use AcMarche\Bottin\Entity\Horaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HoraireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'day',
                HiddenType::class
            )
            ->add(
                'is_open_at_lunch',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => ' ',
                    'attr' => [
                        'class' => 'btnmidi',
                    ],
                    'label_attr' => [
                        'class' => 'checkbox-switch',
                    ],
                ]
            )
            ->add(
                'is_rdv',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => ' ',
                    'label_attr' => [
                        'class' => 'checkbox-switch',
                    ],
                ]
            )
            ->add(
                'is_closed',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => ' ',
                    'attr' => [
                        'class' => 'btnclosed',
                    ],
                    'label_attr' => [
                        'class' => 'checkbox-switch',
                    ],
                ]
            )
            ->add(
                'morning_start',
                TimeType::class,
                [
                    'required' => false,
                    'input' => 'datetime',
                    'widget' => 'single_text',
                    'label' => ' ',
                    'attr' => [
                        'style' => 'width: 100px;',
                        'class' => 'float-end',
                    ],
                ]
            )
            ->add(
                'morning_end',
                TimeType::class,
                [
                    'input' => 'datetime',
                    'required' => false,
                    'widget' => 'single_text',
                    'label' => ' ',
                    'attr' => [
                        'style' => 'width: 100px;',
                        'class' => 'float-end',
                    ],
                ]
            )
            ->add(
                'noon_start',
                TimeType::class,
                [
                    'widget' => 'single_text',
                    'required' => false,
                    'label' => ' ',
                    'attr' => [
                        'style' => 'width: 100px;',
                        'class' => 'float-end',
                    ],
                ]
            )
            ->add(
                'noon_end',
                TimeType::class,
                [
                    'required' => false,
                    'widget' => 'single_text',
                    'attr' => [
                        'style' => 'width: 100px;',
                        'class' => 'float-end',
                    ],
                    'label' => ' ',
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Horaire::class,
            ]
        );
    }
}
